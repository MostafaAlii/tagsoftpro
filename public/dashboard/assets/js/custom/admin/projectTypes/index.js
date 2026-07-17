"use strict";

const ProjectTypes = (() => {
    // ─── Helpers ──────────────────────────────────────────────────────────────

    const route = (name, id) => window.routes[name].replace("__ID__", id);

    const csrfToken = () =>
        document.querySelector('meta[name="csrf-token"]').content;

    const reloadTable = () => {
        try {
            window.LaravelDataTables["project_types_datatable"].ajax.reload(
                null,
                false,
            );
        } catch (e) {
            console.warn("reloadTable fallback:", e);
            $("#project_types_datatable").DataTable().ajax.reload(null, false);
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

        if (method === "POST") {
            return fetch(url, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    Accept: "application/json",
                    "X-CSRF-TOKEN": csrfToken(),
                    "X-Requested-With": "XMLHttpRequest",
                },
                body: JSON.stringify(body),
            });
        }

        const formData = new FormData();
        formData.append("_method", method);
        formData.append("_token", csrfToken());

        const flattenToFormData = (data, prefix = "") => {
            Object.entries(data).forEach(([key, val]) => {
                const k = prefix ? `${prefix}[${key}]` : key;
                if (
                    val !== null &&
                    typeof val === "object" &&
                    !Array.isArray(val)
                ) {
                    flattenToFormData(val, k);
                } else {
                    formData.append(k, val ?? "");
                }
            });
        };
        flattenToFormData(body);

        return fetch(url, {
            method: "POST",
            headers: {
                Accept: "application/json",
                "X-CSRF-TOKEN": csrfToken(),
                "X-Requested-With": "XMLHttpRequest",
            },
            body: formData,
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
        btn.querySelector(".indicator-label").classList.toggle(
            "d-none",
            loading,
        );
        btn.querySelector(".indicator-progress").classList.toggle(
            "d-none",
            !loading,
        );
        btn.disabled = loading;
    };

    const getModal = (id) => bootstrap.Modal.getOrCreateInstance(el(id));
    const hideModal = (id) => bootstrap.Modal.getInstance(el(id))?.hide();

    // ─── Toggle Status ────────────────────────────────────────────────────────

    const initToggleStatus = () => {
        document.addEventListener("change", async (e) => {
            const toggle = e.target.closest(".toggle-status");
            if (!toggle) return;

            const row = toggle.closest("tr");

            try {
                const res = await fetchJson(toggle.dataset.route, "PATCH");
                const data = await parseJson(res);

                if (!data) {
                    toggle.checked = !toggle.checked;
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
                    toggle.checked = !toggle.checked;
                }
            } catch {
                Alert.error(window.translations.error);
                toggle.checked = !toggle.checked;
            }
        });
    };

    // ─── Edit ─────────────────────────────────────────────────────────────────

    const showEditLoader = (show) => {
        el("editModalLoader").classList.toggle("d-none", !show);
        el("editProjectTypeForm").classList.toggle("d-none", show);
    };

    const populateEditForm = (data) => {
        el("editProjectTypeId").value = data.id;

        (data.translations || []).forEach((t) => {
            const nameInput = el(`edit_name_${t.locale}`);
            if (nameInput) nameInput.value = t.name ?? "";

            const descInput = el(`edit_description_${t.locale}`);
            if (descInput) descInput.value = t.description ?? "";
        });

        const firstFilled = (data.translations || []).find(
            (t) => t.name,
        )?.locale;
        if (firstFilled) {
            const tabEl = el(`edit-tab-${firstFilled}`);
            if (tabEl) bootstrap.Tab.getOrCreateInstance(tabEl).show();
        }

        el("edit_company_id").value = data.company_id ?? "";
        el("edit_status").checked = !!+data.status;
    };

    const initEdit = () => {
        // Open modal and fetch data
        document.addEventListener("click", async (e) => {
            const btn = e.target.closest(".btn-edit");
            if (!btn) return;

            getModal("editProjectTypeModal").show();
            showEditLoader(true);

            try {
                const res = await fetchJson(
                    route("edit", btn.dataset.id),
                    "GET",
                );
                const data = await parseJson(res);

                if (data?.success) {
                    populateEditForm(data.data);
                    showEditLoader(false);
                } else {
                    hideModal("editProjectTypeModal");
                    Alert.error(window.translations.error);
                }
            } catch (err) {
                console.error(err);
                hideModal("editProjectTypeModal");
                Alert.error(window.translations.error);
            }
        });

        // Save edit
        document.addEventListener("click", async (e) => {
            if (!e.target.closest("#saveEditProjectType")) return;

            const saveBtn = el("saveEditProjectType");
            const id = el("editProjectTypeId").value;
            const form = el("editProjectTypeForm");

            if (!id) {
                Alert.error(window.translations.error);
                return;
            }

            const payload = {
                name: {},
                description: {},
            };

            (window.locales || []).forEach((locale) => {
                const nameInput = form.querySelector(
                    `[name="name[${locale}]"]`,
                );
                if (nameInput) payload.name[locale] = nameInput.value;

                const descInput = form.querySelector(
                    `[name="description[${locale}]"]`,
                );
                if (descInput) payload.description[locale] = descInput.value;
            });

            payload.company_id =
                form.querySelector('[name="company_id"]')?.value || null;
            payload.status = form.querySelector('[name="status"]')?.checked
                ? 1
                : 0;

            setBtnLoading(saveBtn, true);

            try {
                const res = await fetchJson(
                    route("update", id),
                    "PUT",
                    payload,
                );
                const data = await parseJson(res);

                if (data?.success) {
                    Alert.success(data.message);
                    hideModal("editProjectTypeModal");
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

        // Reset on close
        document.addEventListener("hidden.bs.modal", (e) => {
            if (e.target.id !== "editProjectTypeModal") return;
            el("editProjectTypeForm")?.reset();
            showEditLoader(true);
            const firstTab = document.querySelector("#editLangTabs .nav-link");
            if (firstTab) bootstrap.Tab.getOrCreateInstance(firstTab).show();
        });
    };

    // ─── Delete ───────────────────────────────────────────────────────────────

    const initDelete = () => {
        document.addEventListener("click", (e) => {
            const btn = e.target.closest(".btn-delete");
            if (!btn) return;
            el("deleteProjectTypeId").value = btn.dataset.id;
            getModal("deleteProjectTypeModal").show();
        });

        document.addEventListener("click", async (e) => {
            if (!e.target.closest("#confirmDeleteProjectType")) return;

            const confirmBtn = el("confirmDeleteProjectType");
            const id = el("deleteProjectTypeId").value;
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
                    hideModal("deleteProjectTypeModal");
                    reloadTable();
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
            if (e.target.id !== "deleteProjectTypeModal") return;
            el("deleteProjectTypeId").value = "";
        });
    };

    // ─── Init ─────────────────────────────────────────────────────────────────

    const init = () => {
        initToggleStatus();
        initEdit();
        initDelete();
    };

    return { init };
})();

document.addEventListener("DOMContentLoaded", ProjectTypes.init);
