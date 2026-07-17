"use strict";

const Themes = (() => {
    // ─── Helpers ──────────────────────────────────────────────────────────────

    const route = (name, id, typeId = null) => {
        if (!window.routes || !window.routes[name]) {
            console.error("Route not found:", name);
            return "#";
        }
        let url = window.routes[name].replace("__ID__", id);
        if (typeId) {
            url = url.replace("__TYPE_ID__", typeId);
        }
        return url;
    };

    const csrfToken = () =>
        document.querySelector('meta[name="csrf-token"]')?.content || "";

    const reloadTable = () => {
        try {
            if (
                window.LaravelDataTables &&
                window.LaravelDataTables["themes_datatable"]
            ) {
                window.LaravelDataTables["themes_datatable"].ajax.reload(
                    null,
                    false,
                );
            } else if ($.fn.DataTable) {
                $("#themes_datatable").DataTable().ajax.reload(null, false);
            }
        } catch (e) {
            console.warn("reloadTable fallback:", e);
        }
    };

    const fetchJson = async (url, method, body = {}) => {
        if (!url || url === "#") {
            throw new Error("Invalid URL");
        }

        const options = {
            headers: {
                Accept: "application/json",
                "X-CSRF-TOKEN": csrfToken(),
                "X-Requested-With": "XMLHttpRequest",
            },
        };

        if (method === "GET") {
            options.method = "GET";
        } else if (method === "DELETE") {
            options.method = "DELETE";
            options.headers["Content-Type"] = "application/json";
            options.body = JSON.stringify({});
        } else if (
            method === "PUT" ||
            method === "PATCH" ||
            method === "POST"
        ) {
            options.method = method;
            options.headers["Content-Type"] = "application/json";
            options.body = JSON.stringify(body);
        }

        const res = await fetch(url, options);
        const text = await res.text();
        try {
            return JSON.parse(text);
        } catch {
            console.error("Response is not JSON:", text);
            return null;
        }
    };

    const parseJson = async (res) => {
        if (!res) return null;
        return res;
    };

    const el = (id) => document.getElementById(id);

    const setBtnLoading = (btn, loading) => {
        if (!btn) return;
        const label = btn.querySelector(".indicator-label");
        const progress = btn.querySelector(".indicator-progress");
        if (label) label.classList.toggle("d-none", loading);
        if (progress) progress.classList.toggle("d-none", !loading);
        btn.disabled = loading;
    };

    const getModal = (id) => {
        const element = el(id);
        if (!element) return null;
        return bootstrap.Modal.getOrCreateInstance(element);
    };

    const hideModal = (id) => {
        const instance = bootstrap.Modal.getInstance(el(id));
        if (instance) instance.hide();
    };

    // ─── Handle Default Change (Edit Form) ────────────────────────────────────

    const handleDefaultChange = function () {
        const typeId = this.dataset.typeId;
        const allCheckboxes = document.querySelectorAll(
            `.toggle-default-per-type[data-type-id="${typeId}"]`,
        );
        allCheckboxes.forEach((cb) => {
            if (cb !== this) {
                cb.checked = false;
            }
        });
    };

    // ─── Toggle Status (DataTable inline) ────────────────────────────────────

    const initToggleStatus = () => {
        document.addEventListener("change", async (e) => {
            const toggle = e.target.closest(".toggle-status");
            if (!toggle) return;

            const row = toggle.closest("tr");
            const typeId = toggle.dataset.typeId;

            try {
                const res = await fetchJson(toggle.dataset.route, "PATCH");
                const data = await parseJson(res);

                if (!data) {
                    toggle.checked = !toggle.checked;
                    Alert.error(window.translations?.error || "حدث خطأ");
                    return;
                }

                if (data.success) {
                    Alert.success(data.message);

                    const badgeEl = row?.querySelector(
                        `.badge-status-${typeId}`,
                    );
                    if (badgeEl) {
                        badgeEl.textContent = data.is_active
                            ? window.translations?.active || "مفعل"
                            : window.translations?.inactive || "غير مفعل";
                        badgeEl.className = `badge ${data.is_active ? "bg-success" : "bg-danger"} badge-status-${typeId}`;
                    }

                    toggle.disabled = !data.is_active;
                } else {
                    Alert.error(
                        data.message || window.translations?.error || "حدث خطأ",
                    );
                    toggle.checked = !toggle.checked;
                }
            } catch (error) {
                console.error(error);
                Alert.error(window.translations?.error || "حدث خطأ");
                toggle.checked = !toggle.checked;
            }
        });
    };

    // ─── Toggle Default (DataTable inline) ───────────────────────────────────

    const initToggleDefault = () => {
        document.addEventListener("change", async (e) => {
            const toggle = e.target.closest(".toggle-default");
            if (!toggle) return;

            const row = toggle.closest("tr");
            const typeId = toggle.dataset.typeId;

            try {
                const res = await fetchJson(toggle.dataset.route, "PATCH");
                const data = await parseJson(res);

                if (!data) {
                    toggle.checked = !toggle.checked;
                    Alert.error(window.translations?.error || "حدث خطأ");
                    return;
                }

                if (data.success) {
                    Alert.success(data.message);

                    const badgeEl = row?.querySelector(
                        `.badge-default-${typeId}`,
                    );
                    if (badgeEl) {
                        badgeEl.innerHTML = toggle.checked
                            ? `<span class="badge bg-primary">${window.translations?.default || "افتراضي"}</span>`
                            : `<span class="badge bg-light text-dark">${window.translations?.normal || "عادي"}</span>`;
                    }

                    document
                        .querySelectorAll(
                            `.toggle-default[data-type-id="${typeId}"]`,
                        )
                        .forEach((cb) => {
                            if (cb !== toggle) {
                                cb.checked = false;
                                const badge = cb
                                    .closest("tr")
                                    ?.querySelector(`.badge-default-${typeId}`);
                                if (badge) {
                                    badge.innerHTML = `<span class="badge bg-light text-dark">${window.translations?.normal || "عادي"}</span>`;
                                }
                            }
                        });

                    reloadTable();
                } else {
                    Alert.error(
                        data.message || window.translations?.error || "حدث خطأ",
                    );
                    toggle.checked = !toggle.checked;
                }
            } catch (error) {
                console.error(error);
                Alert.error(window.translations?.error || "حدث خطأ");
                toggle.checked = !toggle.checked;
            }
        });
    };

    // ─── Modals (Read-Only Display) ───────────────────────────────────────────

    const loadModalContent = async (themeId, type) => {
        const modalBodyId =
            type === "default" ? "defaultStatusModalBody" : "statusModalBody";
        const body = document.getElementById(modalBodyId);
        if (!body) return;

        body.innerHTML = `
            <div class="text-center py-3">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        `;

        try {
            const url = route("statuses", themeId);
            if (!url || url === "#") throw new Error("Route not found");

            const res = await fetchJson(url, "GET");

            if (res?.success) {
                body.innerHTML = renderStatusList(res.data, type);
            } else {
                body.innerHTML = `<div class="alert alert-danger">${window.translations?.error || "حدث خطأ"}</div>`;
            }
        } catch (error) {
            console.error("Error loading modal content:", error);
            body.innerHTML = `<div class="alert alert-danger">${window.translations?.error || "حدث خطأ"}</div>`;
        }
    };

    const renderStatusList = (data, type) => {
        if (!data || data.length === 0) {
            return `<div class="text-center text-muted py-3">لا توجد بيانات</div>`;
        }

        // status modal: المفعلين بس
        // default modal: اللي is_default = 1 بس
        const filtered =
            type === "status"
                ? data.filter((item) => item.is_active)
                : data.filter((item) => item.is_default);

        if (filtered.length === 0) {
            return `<div class="text-center text-muted py-3">لا توجد بيانات</div>`;
        }

        let html = '<div class="list-group">';
        filtered.forEach((item) => {
            if (type === "status") {
                const defaultBadge = item.is_default
                    ? `<span class="badge bg-primary ms-1">${window.translations?.default || "افتراضي"}</span>`
                    : "";

                html += `
                    <div class="list-group-item d-flex align-items-center justify-content-between py-3">
                        <strong>${item.name}</strong>
                        <div class="d-flex gap-1">
                            <span class="badge bg-success">${window.translations?.active || "مفعل"}</span>
                            ${defaultBadge}
                        </div>
                    </div>
                `;
            } else {
                html += `
                    <div class="list-group-item d-flex align-items-center justify-content-between py-3">
                        <strong>${item.name}</strong>
                        <span class="badge bg-primary">${window.translations?.default || "افتراضي"}</span>
                    </div>
                `;
            }
        });
        html += "</div>";
        return html;
    };

    const initModals = () => {
        // فتح Default Modal
        document.addEventListener("click", (e) => {
            const btn = e.target.closest(".btn-show-defaults");
            if (!btn) return;
            const nameEl = document.getElementById("defaultModalThemeName");
            if (nameEl) nameEl.textContent = btn.dataset.themeName;
            const modal = getModal("defaultStatusModal");
            if (modal) modal.show();
            loadModalContent(btn.dataset.themeId, "default");
        });

        // فتح Status Modal
        document.addEventListener("click", (e) => {
            const btn = e.target.closest(".btn-show-statuses");
            if (!btn) return;
            const nameEl = document.getElementById("statusModalThemeName");
            if (nameEl) nameEl.textContent = btn.dataset.themeName;
            const modal = getModal("statusModal");
            if (modal) modal.show();
            loadModalContent(btn.dataset.themeId, "status");
        });
    };

    // ─── Edit ─────────────────────────────────────────────────────────────────

    const showEditLoader = (show) => {
        const loader = el("editModalLoader");
        const form = el("editThemeForm");
        if (loader) loader.classList.toggle("d-none", !show);
        if (form) form.classList.toggle("d-none", show);
    };

    const populateEditForm = (data) => {
        el("editThemeId").value = data.id;
        el("edit_name").value = data.name ?? "";
        el("edit_code").value = data.code ?? "";
        el("edit_description").value = data.description ?? "";
        el("edit_paid_type").value = data.paid_type ?? "free";
        el("edit_price").value = data.price ?? "";
        el("edit_company_id").value = data.company_id ?? "";

        const priceWrapper = document.getElementById("edit_price_wrapper");
        if (priceWrapper) {
            priceWrapper.style.display =
                data.paid_type === "paid" ? "block" : "none";
        }

        const container = document.getElementById("editProjectTypesContainer");
        if (!container) return;

        container.innerHTML = "";

        if (data.project_types && data.project_types.length > 0) {
            data.project_types.forEach((pt) => {
                const isActive = pt.pivot?.is_active ?? false;
                const isDefault = pt.pivot?.is_default ?? false;

                const div = document.createElement("div");
                div.className =
                    "row mb-3 align-items-center border-bottom pb-2";
                div.innerHTML = `
                    <div class="col-md-4">
                        <strong>${pt.translated_name || pt.name}</strong>
                    </div>
                    <div class="col-md-4">
                        <div class="form-check form-switch">
                            <input type="hidden" name="project_types[${pt.id}][is_active]" value="0">
                            <input class="form-check-input" type="checkbox"
                                name="project_types[${pt.id}][is_active]"
                                value="1"
                                ${isActive ? "checked" : ""}>
                            <label class="form-check-label">${window.translations?.active || "مفعل"}</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-check form-switch">
                            <input type="hidden" name="project_types[${pt.id}][is_default]" value="0">
                            <input class="form-check-input toggle-default-per-type"
                                type="checkbox"
                                name="project_types[${pt.id}][is_default]"
                                value="1"
                                data-type-id="${pt.id}"
                                ${isDefault ? "checked" : ""}>
                            <label class="form-check-label">${window.translations?.default || "افتراضي"}</label>
                        </div>
                    </div>
                `;
                container.appendChild(div);
            });
        }

        document
            .querySelectorAll(".toggle-default-per-type")
            .forEach((checkbox) => {
                checkbox.removeEventListener("change", handleDefaultChange);
                checkbox.addEventListener("change", handleDefaultChange);
            });
    };

    const initEdit = () => {
        // فتح Edit Modal
        document.addEventListener("click", async (e) => {
            const btn = e.target.closest(".btn-edit");
            if (!btn) return;

            const modal = getModal("editThemeModal");
            if (modal) modal.show();
            showEditLoader(true);

            try {
                const url = route("edit", btn.dataset.id);
                if (!url || url === "#") throw new Error("Route not found");

                const res = await fetchJson(url, "GET");
                const data = await parseJson(res);

                if (data?.success) {
                    populateEditForm(data.data);
                    showEditLoader(false);
                } else {
                    if (modal) modal.hide();
                    Alert.error(window.translations?.error || "حدث خطأ");
                }
            } catch (err) {
                console.error(err);
                if (modal) modal.hide();
                Alert.error(window.translations?.error || "حدث خطأ");
            }
        });

        // حفظ Edit
        document.addEventListener("click", async (e) => {
            if (!e.target.closest("#saveEditTheme")) return;

            const saveBtn = el("saveEditTheme");
            const id = el("editThemeId").value;
            const form = el("editThemeForm");

            if (!id || !saveBtn) {
                Alert.error(window.translations?.error || "حدث خطأ");
                return;
            }

            const payload = {
                name: form?.querySelector('[name="name"]')?.value,
                code: form?.querySelector('[name="code"]')?.value,
                description: form?.querySelector('[name="description"]')?.value,
                paid_type: form?.querySelector('[name="paid_type"]')?.value,
                price: form?.querySelector('[name="price"]')?.value,
                company_id: form?.querySelector('[name="company_id"]')?.value,
                project_types: {},
            };

            // جمع بيانات Project Types — checkbox فقط مش hidden
            document
                .querySelectorAll("#editProjectTypesContainer .row")
                .forEach((row) => {
                    const activeInput = row.querySelector(
                        'input[type="checkbox"][name$="[is_active]"]',
                    );
                    const defaultInput = row.querySelector(
                        'input[type="checkbox"][name$="[is_default]"]',
                    );

                    if (activeInput) {
                        const nameMatch = activeInput.name.match(
                            /project_types\[(\d+)\]\[is_active\]/,
                        );
                        if (nameMatch) {
                            const typeId = nameMatch[1];
                            payload.project_types[typeId] = {
                                is_active: activeInput.checked ? 1 : 0,
                                is_default: defaultInput
                                    ? defaultInput.checked
                                        ? 1
                                        : 0
                                    : 0,
                            };
                        }
                    }
                });

            setBtnLoading(saveBtn, true);

            try {
                const url = route("update", id);
                if (!url || url === "#") throw new Error("Route not found");

                const res = await fetchJson(url, "PUT", payload);
                const data = await parseJson(res);

                if (data?.success) {
                    Alert.success(data.message);
                    const modal = getModal("editThemeModal");
                    if (modal) modal.hide();
                    reloadTable();
                } else {
                    Alert.error(
                        data?.message ||
                            window.translations?.error ||
                            "حدث خطأ",
                    );
                }
            } catch (err) {
                console.error(err);
                Alert.error(window.translations?.error || "حدث خطأ");
            } finally {
                setBtnLoading(saveBtn, false);
            }
        });

        // Reset عند إغلاق Modal
        document.addEventListener("hidden.bs.modal", (e) => {
            if (e.target.id !== "editThemeModal") return;
            const form = el("editThemeForm");
            if (form) form.reset();
            showEditLoader(true);
        });
    };

    // ─── Delete ───────────────────────────────────────────────────────────────

    const initDelete = () => {
        document.addEventListener("click", (e) => {
            const btn = e.target.closest(".btn-delete");
            if (!btn) return;
            const deleteId = el("deleteThemeId");
            if (deleteId) deleteId.value = btn.dataset.id;
            const modal = getModal("deleteThemeModal");
            if (modal) modal.show();
        });

        document.addEventListener("click", async (e) => {
            if (!e.target.closest("#confirmDeleteTheme")) return;

            const confirmBtn = el("confirmDeleteTheme");
            const id = el("deleteThemeId")?.value;
            if (!id || !confirmBtn) return;

            setBtnLoading(confirmBtn, true);

            try {
                const url = route("destroy", id);
                if (!url || url === "#") throw new Error("Route not found");

                const res = await fetchJson(url, "DELETE");
                const data = await parseJson(res);

                if (!data) {
                    Alert.error(window.translations?.error || "حدث خطأ");
                    return;
                }

                if (data.success) {
                    Alert.success(data.message);
                    const modal = getModal("deleteThemeModal");
                    if (modal) modal.hide();
                    reloadTable();
                } else {
                    Alert.error(
                        data.message || window.translations?.error || "حدث خطأ",
                    );
                }
            } catch (err) {
                console.error("Delete Error:", err);
                Alert.error(window.translations?.error || "حدث خطأ");
            } finally {
                setBtnLoading(confirmBtn, false);
            }
        });

        document.addEventListener("hidden.bs.modal", (e) => {
            if (e.target.id !== "deleteThemeModal") return;
            const deleteId = el("deleteThemeId");
            if (deleteId) deleteId.value = "";
        });
    };

    // ─── Init ─────────────────────────────────────────────────────────────────

    const init = () => {
        console.log("Themes.init() started");
        initToggleStatus();
        initToggleDefault();
        initEdit();
        initDelete();
        initModals();
        console.log("Themes.init() finished");
    };

    return { init };
})();

document.addEventListener("DOMContentLoaded", Themes.init);
