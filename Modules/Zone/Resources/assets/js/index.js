/**
 * ============================================================
 * 📁 FILE: index.js
 * 📌 MAIN FUNCTIONS: Zones CRUD Operations
 * ============================================================
 */

"use strict";

// ─── Helpers ──────────────────────────────────────────────────────────────
const route = (name, id) => window.routes[name].replace("__ID__", id);

const csrfToken = () =>
    document.querySelector('meta[name="csrf-token"]').content;

const reloadTable = () => {
    try {
        window.LaravelDataTables["zones_datatable"].ajax.reload(null, false);
    } catch (e) {
        console.warn("reloadTable fallback:", e);
        $("#zones_datatable").DataTable().ajax.reload(null, false);
    }
};

const fetchJson = async (url, method, body = {}) => {
    if (method === "GET") {
        return fetch(url, {
            method: "GET",
            headers: {
                Accept: "application/json",
                "X-CSRF-TOKEN": csrfToken(),
                "X-Requested-With": "XMLHttpRequest",
            },
        });
    }

    if (method === "DELETE") {
        return fetch(url, {
            method: "DELETE",
            headers: {
                Accept: "application/json",
                "X-CSRF-TOKEN": csrfToken(),
                "X-Requested-With": "XMLHttpRequest",
                "Content-Type": "application/json",
            },
            body: JSON.stringify({}),
        });
    }

    if (method === "PUT" || method === "PATCH" || method === "POST") {
        return fetch(url, {
            method: method,
            headers: {
                "Content-Type": "application/json",
                Accept: "application/json",
                "X-CSRF-TOKEN": csrfToken(),
                "X-Requested-With": "XMLHttpRequest",
            },
            body: JSON.stringify(body),
        });
    }

    return fetch(url, {
        method: method,
        headers: {
            Accept: "application/json",
            "X-CSRF-TOKEN": csrfToken(),
            "X-Requested-With": "XMLHttpRequest",
        },
    });
};

const parseJson = async (res) => {
    const text = await res.text();
    try {
        return JSON.parse(text);
    } catch {
        console.error("Response is not JSON:", text);
        return null;
    }
};

const el = (id) => document.getElementById(id);

const setBtnLoading = (btn, loading) => {
    if (!btn) return;

    const indicatorLabel = btn.querySelector(".indicator-label");
    const indicatorProgress = btn.querySelector(".indicator-progress");

    if (indicatorLabel) {
        indicatorLabel.classList.toggle("d-none", loading);
    }
    if (indicatorProgress) {
        indicatorProgress.classList.toggle("d-none", !loading);
    }
    btn.disabled = loading;
};

const getModal = (id) => bootstrap.Modal.getOrCreateInstance(el(id));
const hideModal = (id) => bootstrap.Modal.getInstance(el(id))?.hide();

// ─── Confirm Modal ──────────────────────────────────────────────────────────
let confirmCallback = null;

const showConfirmModal = (title, message, confirmText = "تأكيد", callback) => {
    document.getElementById("confirmActionTitle").textContent = title;
    document.getElementById("confirmActionBody").textContent = message;
    const confirmBtn = document.getElementById("confirmActionBtn");
    confirmBtn.querySelector(".indicator-label").textContent = confirmText;
    confirmBtn.classList.remove("btn-success", "btn-danger", "btn-primary");
    confirmBtn.classList.add("btn-danger");

    confirmCallback = callback;

    const modal = new bootstrap.Modal(
        document.getElementById("confirmActionModal"),
    );
    modal.show();
};

document.addEventListener("click", async function (e) {
    const confirmBtn = e.target.closest("#confirmActionBtn");
    if (!confirmBtn) return;

    if (typeof confirmCallback === "function") {
        setBtnLoading(confirmBtn, true);
        await confirmCallback(confirmBtn);
        setBtnLoading(confirmBtn, false);
    }

    const modal = bootstrap.Modal.getInstance(
        document.getElementById("confirmActionModal"),
    );
    if (modal) modal.hide();
    confirmCallback = null;
});

// ─── Toggle Status ────────────────────────────────────────────────────────
const initToggleStatus = () => {
    document.addEventListener("click", async (e) => {
        const btn = e.target.closest(".toggle-status");
        if (!btn) return;

        const row = btn.closest("tr");

        try {
            const res = await fetchJson(btn.dataset.route, "PATCH");
            const data = await parseJson(res);

            if (!data) {
                Alert.error(window.translations.error);
                return;
            }

            if (data.success) {
                Alert.success(data.message);
                const badgeEl = row?.querySelector(".badge-status");
                if (badgeEl)
                    badgeEl.outerHTML = `<span class="badge-status">${data.badge}</span>`;
            } else {
                Alert.error(data.message || window.translations.error);
            }
        } catch {
            Alert.error(window.translations.error);
        }
    });
};

// ─── Edit ─────────────────────────────────────────────────────────────────
const showEditLoader = (show) => {
    const loader = document.getElementById("editModalLoader");
    const form = document.getElementById("editZoneForm");

    if (loader) loader.classList.toggle("d-none", !show);
    if (form) form.classList.toggle("d-none", show);
};

const populateEditForm = (data) => {
    const zoneIdInput = document.getElementById("modal_edit_zone_id");
    if (zoneIdInput) zoneIdInput.value = data.id;

    const keyInput = document.getElementById("modal_edit_key");
    if (keyInput) keyInput.value = data.key ?? "";

    const statusSelect = document.getElementById("modal_edit_status");
    if (statusSelect) statusSelect.value = data.status ?? "active";

    const locales = document.querySelectorAll(
        '#editZoneForm [name^="locales["]',
    );
    locales.forEach((input) => {
        const match = input.name.match(/locales\[([^\]]+)\]\[([^\]]+)\]/);
        if (match) {
            const locale = match[1];
            const field = match[2];
            const translation = data.translations?.find(
                (t) => t.locale === locale,
            );
            if (translation) {
                input.value = translation[field] ?? "";
            } else {
                input.value = "";
            }
        }
    });
};

const initEdit = () => {
    document.addEventListener("click", async (e) => {
        const btn = e.target.closest(".btn-edit");
        if (!btn) return;

        const modal = getModal("editZoneModal");
        modal.show();
        showEditLoader(true);

        try {
            const res = await fetchJson(route("edit", btn.dataset.id), "GET");
            const data = await parseJson(res);

            if (data?.success) {
                populateEditForm(data.data);
                showEditLoader(false);
            } else {
                hideModal("editZoneModal");
                Alert.error(window.translations.error);
            }
        } catch (err) {
            console.error(err);
            hideModal("editZoneModal");
            Alert.error(window.translations.error);
        }
    });

    document.addEventListener("click", async (e) => {
        const saveBtn = e.target.closest("#modal_save_edit_zone");
        if (!saveBtn) return;

        const zoneIdInput = document.getElementById("modal_edit_zone_id");
        const id = zoneIdInput?.value;
        const form = document.getElementById("editZoneForm");

        if (!id) {
            Alert.error(window.translations.error);
            return;
        }

        const formData = new FormData();
        formData.append("_method", "PUT");
        formData.append("_token", csrfToken());

        const fields = ["key", "status"];
        fields.forEach((field) => {
            const input = form.querySelector(`[name="${field}"]`);
            if (input) {
                formData.append(field, input.value ?? "");
            }
        });

        const translationInputs = form.querySelectorAll('[name^="locales["]');
        translationInputs.forEach((input) => {
            formData.append(input.name, input.value ?? "");
        });

        setBtnLoading(saveBtn, true);

        try {
            const response = await fetch(route("update", id), {
                method: "POST",
                headers: {
                    Accept: "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                },
                body: formData,
            });
            const data = await parseJson(response);

            if (data?.success) {
                Alert.success(data.message);
                hideModal("editZoneModal");
                reloadTable();
            } else {
                Alert.error(data?.message || window.translations.error);
            }
        } catch (err) {
            console.error(err);
            Alert.error(window.translations.error);
        } finally {
            setBtnLoading(saveBtn, false);
        }
    });

    document.addEventListener("hidden.bs.modal", (e) => {
        if (e.target.id !== "editZoneModal") return;
        const form = document.getElementById("editZoneForm");
        if (form) form.reset();
        showEditLoader(true);
    });
};

// ─── Delete ───────────────────────────────────────────────────────────────
const initDelete = () => {
    document.addEventListener("click", (e) => {
        const btn = e.target.closest(".btn-delete");
        if (!btn) return;

        const row = btn.closest("tr");
        const nameCell = row?.querySelector("td:nth-child(3)");
        const zoneName = nameCell?.textContent?.trim() || "";

        const confirmMessage = document.querySelector(
            "#deleteZoneModal .modal-body p",
        );
        if (confirmMessage) {
            confirmMessage.textContent =
                window.translations.delete_confirm?.replace(
                    ":name",
                    zoneName,
                ) || `هل أنت متأكد من حذف المنطقة "${zoneName}"؟`;
        }

        const deleteIdInput = document.getElementById("deleteZoneId");
        if (deleteIdInput) deleteIdInput.value = btn.dataset.id;

        getModal("deleteZoneModal").show();
    });

    document.addEventListener("click", async (e) => {
        if (!e.target.closest("#confirmDeleteZone")) return;

        const confirmBtn = document.getElementById("confirmDeleteZone");
        const deleteIdInput = document.getElementById("deleteZoneId");
        const id = deleteIdInput?.value;

        if (!id) return;

        setBtnLoading(confirmBtn, true);

        try {
            const res = await fetchJson(route("destroy", id), "DELETE");
            const data = await parseJson(res);

            if (!data) {
                Alert.error(window.translations.error);
                return;
            }

            if (data.success) {
                Alert.success(data.message);
                hideModal("deleteZoneModal");
                reloadTable();
                setTimeout(window.checkTrashed, 500);
            } else {
                Alert.error(data.message || window.translations.error);
            }
        } catch (err) {
            console.error("Delete Error:", err);
            Alert.error(window.translations.error);
        } finally {
            setBtnLoading(confirmBtn, false);
        }
    });

    document.addEventListener("hidden.bs.modal", (e) => {
        if (e.target.id !== "deleteZoneModal") return;
        const deleteIdInput = document.getElementById("deleteZoneId");
        if (deleteIdInput) deleteIdInput.value = "";
    });
};

// ─── Restore ───────────────────────────────────────────────────────────────
const initRestore = () => {
    document.addEventListener("click", async (e) => {
        const btn = e.target.closest(".btn-restore");
        if (!btn) return;

        const routeUrl = btn.dataset.route;
        if (!routeUrl) {
            Alert.error("Route not found");
            return;
        }

        showConfirmModal(
            window.translations.restore || "استعادة",
            window.translations.restore_confirm ||
                "هل أنت متأكد من استعادة هذه المنطقة؟",
            window.translations.restore || "استعادة",
            async (confirmBtn) => {
                try {
                    const res = await fetchJson(routeUrl, "PATCH");
                    const data = await parseJson(res);
                    if (data?.success) {
                        Alert.success(data.message);
                        reloadTable();
                        setTimeout(window.checkTrashed, 500);
                    } else {
                        Alert.error(data?.message || window.translations.error);
                    }
                } catch (err) {
                    console.error(err);
                    Alert.error(window.translations.error);
                }
            },
        );
    });
};

// ─── Force Delete ───────────────────────────────────────────────────────────
const initForceDelete = () => {
    document.addEventListener("click", async (e) => {
        const btn = e.target.closest(".btn-force-delete");
        if (!btn) return;

        const routeUrl = btn.dataset.route;
        if (!routeUrl) {
            Alert.error("Route not found");
            return;
        }

        showConfirmModal(
            window.translations.force_delete || "حذف نهائي",
            window.translations.force_delete_confirm ||
                "تحذير! هذا الإجراء لا يمكن التراجع عنه. هل أنت متأكد؟",
            window.translations.force_delete || "حذف نهائي",
            async (confirmBtn) => {
                try {
                    const res = await fetchJson(routeUrl, "DELETE");
                    const data = await parseJson(res);
                    if (data?.success) {
                        Alert.success(data.message);
                        reloadTable();
                        setTimeout(window.checkTrashed, 500);
                    } else {
                        Alert.error(data?.message || window.translations.error);
                    }
                } catch (err) {
                    console.error(err);
                    Alert.error(window.translations.error);
                }
            },
        );
    });
};

// ─── Init ─────────────────────────────────────────────────────────────────
const init = () => {
    initToggleStatus();
    initEdit();
    initDelete();
    initRestore();
    initForceDelete();
};

window.Zones = {
    init: init,
    reloadTable: reloadTable,
    fetchJson: fetchJson,
    parseJson: parseJson,
    setBtnLoading: setBtnLoading,
    showConfirmModal: showConfirmModal,
    getModal: getModal,
    hideModal: hideModal,
};
