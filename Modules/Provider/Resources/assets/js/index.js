/**
 * ============================================================
 * 📁 FILE: index.js
 * 📌 MAIN FUNCTIONS: Providers CRUD Operations
 * ============================================================
 */

"use strict";

// ─── Helpers ──────────────────────────────────────────────────────────────
const route = (name, id) => window.routes[name].replace("__ID__", id);

const csrfToken = () =>
    document.querySelector('meta[name="csrf-token"]').content;

const reloadTable = () => {
    try {
        window.LaravelDataTables["providers_datatable"].ajax.reload(
            null,
            false,
        );
    } catch (e) {
        console.warn("reloadTable fallback:", e);
        $("#providers_datatable").DataTable().ajax.reload(null, false);
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
    const form = document.getElementById("editProviderForm");

    if (loader) loader.classList.toggle("d-none", !show);
    if (form) form.classList.toggle("d-none", show);
};

const populateEditForm = (data) => {
    const providerIdInput = document.getElementById("modal_edit_provider_id");
    if (providerIdInput) providerIdInput.value = data.id;

    const nameInput = document.getElementById("modal_edit_name");
    if (nameInput) nameInput.value = data.name ?? "";

    const emailInput = document.getElementById("modal_edit_email");
    if (emailInput) emailInput.value = data.email ?? "";

    const phoneInput = document.getElementById("modal_edit_phone");
    if (phoneInput) phoneInput.value = data.phone ?? "";

    const statusSelect = document.getElementById("modal_edit_status");
    if (statusSelect) statusSelect.value = data.status ?? "pending";

    const dateInput = document.getElementById("modal_edit_date");
    if (dateInput) dateInput.value = data.date ? data.date.split("T")[0] : "";

    const addressInput = document.getElementById("modal_edit_address");
    if (addressInput) addressInput.value = data.address ?? "";

    const websiteInput = document.getElementById("modal_edit_website");
    if (websiteInput) websiteInput.value = data.website ?? "";

    const zoneSelect = document.getElementById("modal_edit_zone_id");
    if (zoneSelect) zoneSelect.value = data.zone_id ?? "";
};

const initEdit = () => {
    document.addEventListener("click", async (e) => {
        const btn = e.target.closest(".btn-edit");
        if (!btn) return;

        const modal = getModal("editProviderModal");
        modal.show();
        showEditLoader(true);

        try {
            const res = await fetchJson(route("edit", btn.dataset.id), "GET");
            const data = await parseJson(res);

            if (data?.success) {
                populateEditForm(data.data);
                showEditLoader(false);
            } else {
                hideModal("editProviderModal");
                Alert.error(window.translations.error);
            }
        } catch (err) {
            console.error(err);
            hideModal("editProviderModal");
            Alert.error(window.translations.error);
        }
    });

    document.addEventListener("click", async (e) => {
        const saveBtn = e.target.closest("#modal_save_edit_provider");
        if (!saveBtn) return;

        const providerIdInput = document.getElementById(
            "modal_edit_provider_id",
        );
        const id = providerIdInput?.value;
        const form = document.getElementById("editProviderForm");

        if (!id) {
            Alert.error(window.translations.error);
            return;
        }

        const formData = new FormData();
        formData.append("_method", "PUT");
        formData.append("_token", csrfToken());

        const fields = [
            "name",
            "email",
            "phone",
            "password",
            "status",
            "date",
            "address",
            "website",
            "zone_id",
        ];
        fields.forEach((field) => {
            const input = form.querySelector(`[name="${field}"]`);
            if (input) {
                formData.append(field, input.value ?? "");
            }
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
                hideModal("editProviderModal");
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
        if (e.target.id !== "editProviderModal") return;
        const form = document.getElementById("editProviderForm");
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
        const nameCell = row?.querySelector("td:nth-child(2)");
        const providerName = nameCell?.textContent?.trim() || "";

        const confirmMessage = document.querySelector(
            "#deleteProviderModal .modal-body p",
        );
        if (confirmMessage) {
            confirmMessage.textContent =
                window.translations.delete_confirm?.replace(
                    ":name",
                    providerName,
                ) || `هل أنت متأكد من حذف مزود الخدمة "${providerName}"؟`;
        }

        const deleteIdInput = document.getElementById("deleteProviderId");
        if (deleteIdInput) deleteIdInput.value = btn.dataset.id;

        getModal("deleteProviderModal").show();
    });

    document.addEventListener("click", async (e) => {
        if (!e.target.closest("#confirmDeleteProvider")) return;

        const confirmBtn = document.getElementById("confirmDeleteProvider");
        const deleteIdInput = document.getElementById("deleteProviderId");
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
                hideModal("deleteProviderModal");
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
        if (e.target.id !== "deleteProviderModal") return;
        const deleteIdInput = document.getElementById("deleteProviderId");
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
                "هل أنت متأكد من استعادة مزود الخدمة هذا؟",
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

window.Providers = {
    init: init,
    reloadTable: reloadTable,
    fetchJson: fetchJson,
    parseJson: parseJson,
    setBtnLoading: setBtnLoading,
    showConfirmModal: showConfirmModal,
    getModal: getModal,
    hideModal: hideModal,
};
