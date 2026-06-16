"use strict";

const Modules = (() => {
    // ─── Helpers ──────────────────────────────────────────────────────────────

    const route = (name, id) => window.routes[name].replace("__ID__", id);

    const csrfToken = () =>
        document.querySelector('meta[name="csrf-token"]').content;

    const reloadTable = () => {
        try {
            window.LaravelDataTables["modules_datatable"].ajax.reload(
                null,
                false,
            );
        } catch (e) {
            console.warn("reloadTable fallback:", e);
            $("#modules_datatable").DataTable().ajax.reload(null, false);
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
        el("editModuleForm").classList.toggle("d-none", show);
    };

    const populateEditForm = (data) => {
        el("editModuleId").value = data.id;

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

        // ✅ Project Types - Checkboxes (نسخة مضمونة)
        const projectTypeIds = [];

        // لو data.project_types عبارة عن array من كائنات
        if (data.project_types && Array.isArray(data.project_types)) {
            data.project_types.forEach((pt) => {
                if (pt.id) {
                    projectTypeIds.push(pt.id);
                }
            });
        }

        // لو data.project_types عبارة عن array من IDs
        if (data.project_type_ids && Array.isArray(data.project_type_ids)) {
            data.project_type_ids.forEach((id) => {
                projectTypeIds.push(id);
            });
        }


        // حدد الـ checkboxes
        document
            .querySelectorAll('#editModuleForm input[name="project_types[]"]')
            .forEach((checkbox) => {
                const val = parseInt(checkbox.value);
                checkbox.checked = projectTypeIds.includes(val);
            });

        el("edit_company_id").value = data.company_id ?? "";
        el("edit_status").checked = !!+data.status;
    };

    const initEdit = () => {
        // Open modal and fetch data
        document.addEventListener("click", async (e) => {
            const btn = e.target.closest(".btn-edit");
            if (!btn) return;

            getModal("editModuleModal").show();
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
                    hideModal("editModuleModal");
                    Alert.error(window.translations.error);
                }
            } catch (err) {
                console.error(err);
                hideModal("editModuleModal");
                Alert.error(window.translations.error);
            }
        });

        // Save edit
        document.addEventListener("click", async (e) => {
            if (!e.target.closest("#saveEditModule")) return;

            const saveBtn = el("saveEditModule");
            const id = el("editModuleId").value;
            const form = el("editModuleForm");

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

            // ✅ جلب الـ project_types المحددة (Checkboxes)
            const projectTypes = [];
            form.querySelectorAll(
                'input[name="project_types[]"]:checked',
            ).forEach((checkbox) => {
                projectTypes.push(parseInt(checkbox.value));
            });

            // ✅ تحويل array إلى string مفصول بفواصل (لـ FormData)
            payload.project_types = projectTypes.join(",");

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
                    hideModal("editModuleModal");
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
            if (e.target.id !== "editModuleModal") return;
            el("editModuleForm")?.reset();
            showEditLoader(true);
            const firstTab = document.querySelector("#editLangTabs .nav-link");
            if (firstTab) bootstrap.Tab.getOrCreateInstance(firstTab).show();
        });
    };;

    // ─── Delete ───────────────────────────────────────────────────────────────

    const initDelete = () => {
        document.addEventListener("click", (e) => {
            const btn = e.target.closest(".btn-delete");
            if (!btn) return;
            el("deleteModuleId").value = btn.dataset.id;
            getModal("deleteModuleModal").show();
        });

        document.addEventListener("click", async (e) => {
            if (!e.target.closest("#confirmDeleteModule")) return;

            const confirmBtn = el("confirmDeleteModule");
            const id = el("deleteModuleId").value;
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
                    hideModal("deleteModuleModal");
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
            if (e.target.id !== "deleteModuleModal") return;
            el("deleteModuleId").value = "";
        });
    };

    // ─── Show Project Types Modal ───────────────────────────────────────────────

    const initProjectTypesModal = () => {
        document.addEventListener("click", (e) => {
            const badge = e.target.closest(
                '[data-bs-target="#projectTypesModal"]',
            );
            if (!badge) return;

            const moduleName = badge.dataset.moduleName || "";
            const projectTypesData = badge.dataset.projectTypes;

            // Update modal title
            document.getElementById("projectTypesModalTitle").textContent =
                `${window.translations.project_types_for} "${moduleName}"`;

            // Update modal body
            const listContainer = document.getElementById("projectTypesList");

            if (projectTypesData) {
                try {
                    const projectTypes = JSON.parse(projectTypesData);

                    if (projectTypes.length === 0) {
                        listContainer.innerHTML = `
                        <div class="text-center text-muted py-3">
                            <i class="ti ti-folder-off fs-2 d-block mb-2"></i>
                            ${window.translations.no_project_types}
                        </div>
                    `;
                    } else {
                        listContainer.innerHTML = projectTypes
                            .map(
                                (pt) => `
                        <span class="badge bg-secondary p-2 fs-6">
                            <i class="ti ti-folder me-1"></i>
                            ${pt.name}
                        </span>
                    `,
                            )
                            .join("");
                    }
                } catch (err) {
                    console.error("Error parsing project types:", err);
                    listContainer.innerHTML = `
                    <div class="text-center text-danger py-3">
                        ${window.translations.error_occurred}
                    </div>
                `;
                }
            } else {
                listContainer.innerHTML = `
                <div class="text-center text-muted py-3">
                    ${window.translations.no_project_types}
                </div>
            `;
            }
        });
    };

    // ─── Init ─────────────────────────────────────────────────────────────────

    const init = () => {
        initToggleStatus();
        initEdit();
        initDelete();
        initProjectTypesModal();
    };

    return { init };
})();

document.addEventListener("DOMContentLoaded", Modules.init);
