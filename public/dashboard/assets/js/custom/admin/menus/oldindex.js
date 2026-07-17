// index.js
/**
 * ============================================================
 * 📁 FILE: index.js
 * 📌 MAIN FUNCTIONS: Menus CRUD Operations
 * ============================================================
 */

"use strict";

// ─── Helpers ──────────────────────────────────────────────────────────────
const route = (name, id) => window.routes[name].replace("__ID__", id);

const csrfToken = () =>
    document.querySelector('meta[name="csrf-token"]').content;

const reloadTable = () => {
    try {
        window.LaravelDataTables["menus_datatable"].ajax.reload(
            null,
            false,
        );
    } catch (e) {
        console.warn("reloadTable fallback:", e);
        $("#menus_datatable").DataTable().ajax.reload(null, false);
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
    const form = document.getElementById("editMenuForm");

    if (loader) loader.classList.toggle("d-none", !show);
    if (form) form.classList.toggle("d-none", show);
};

const populateEditForm = (data) => {
    // استخدم الـ IDs الجديدة بتاعة المودال
    const menuIdInput = document.getElementById("modal_edit_menu_id");
    if (menuIdInput) menuIdInput.value = data.id;

    const keyInput = document.getElementById("modal_edit_key");
    if (keyInput) keyInput.value = data.key ?? "";

    const iconInput = document.getElementById("modal_edit_icon");
    if (iconInput) iconInput.value = data.icon ?? "";

    const routePrefixInput = document.getElementById("modal_edit_route_prefix");
    if (routePrefixInput) routePrefixInput.value = data.route_prefix ?? "";

    const statusSelect = document.getElementById("modal_edit_status");
    if (statusSelect) statusSelect.value = data.status ?? "active";

    const companySelect = document.getElementById("modal_edit_company_id");
    if (companySelect) {
        companySelect.value = data.company_id ?? "";
    }

    // ─── Icon ────────────────────────────────────────────────
    const iconSelect = document.getElementById("modal_edit_icon");
    const iconPreview = document.getElementById("modal_icon_preview");
    
    if (iconSelect) {
        const iconValue = data.icon ?? '';
        // ─── نستخدم الدالة الجديدة ────────────────────────────
        setIconPickerValue('modal_edit_icon', 'modal_icon_preview', iconValue);
    }

    // الترجمة - استخدم الـ IDs الجديدة
    const locales = document.querySelectorAll('#editMenuForm [name^="locales["]');
    locales.forEach((input) => {
        const match = input.name.match(/locales\[([^\]]+)\]\[([^\]]+)\]/);
        if (match) {
            const locale = match[1];
            const field = match[2];
            const translation = data.translations?.find(
                (t) => t.locale === locale
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

        const modal = getModal("editMenuModal");
        modal.show();
        showEditLoader(true);

        try {
            const res = await fetchJson(route("edit", btn.dataset.id), "GET");
            const data = await parseJson(res);

            if (data?.success) {
                populateEditForm(data.data);
                showEditLoader(false);
            } else {
                hideModal("editMenuModal");
                Alert.error(window.translations.error);
            }
        } catch (err) {
            console.error(err);
            hideModal("editMenuModal");
            Alert.error(window.translations.error);
        }
    });

    document.addEventListener("click", async (e) => {
        const saveBtn = e.target.closest("#modal_save_edit_menu");
        if (!saveBtn) return;

        const menuIdInput = document.getElementById("modal_edit_menu_id");
        const id = menuIdInput?.value;
        const form = document.getElementById("editMenuForm");

        if (!id) {
            Alert.error(window.translations.error);
            return;
        }

        const formData = new FormData();
        formData.append("_method", "PUT");
        formData.append("_token", csrfToken());

        const fields = ["key", "icon", "route_prefix", "status"];
        fields.forEach((field) => {
            const input = form.querySelector(`[name="${field}"]`);
            if (input) {
                formData.append(field, input.value ?? "");
            }
        });

        const companyInput = form.querySelector('[name="company_id"]');
        if (companyInput) {
            formData.append("company_id", companyInput.value ?? "");
        }

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
                hideModal("editMenuModal");
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
        if (e.target.id !== "editMenuModal") return;
        const form = document.getElementById("editMenuForm");
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
        const menuName = nameCell?.textContent?.trim() || '';

        const confirmMessage = document.querySelector("#deleteMenuModal .modal-body p");
        if (confirmMessage) {
            confirmMessage.textContent = window.translations.delete_confirm?.replace(':name', menuName)
                || `هل أنت متأكد من حذف القائمة "${menuName}"؟`;
        }

        const deleteIdInput = document.getElementById("deleteMenuId");
        if (deleteIdInput) deleteIdInput.value = btn.dataset.id;

        getModal("deleteMenuModal").show();
    });

    document.addEventListener("click", async (e) => {
        if (!e.target.closest("#confirmDeleteMenu")) return;

        const confirmBtn = document.getElementById("confirmDeleteMenu");
        const deleteIdInput = document.getElementById("deleteMenuId");
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
                hideModal("deleteMenuModal");
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
        if (e.target.id !== "deleteMenuModal") return;
        const deleteIdInput = document.getElementById("deleteMenuId");
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
            Alert.error('Route not found');
            return;
        }

        showConfirmModal(
            window.translations.restore || "استعادة",
            window.translations.restore_confirm ||
                "هل أنت متأكد من استعادة هذه القائمة؟",
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
            Alert.error('Route not found');
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

// ─── Icon Picker - Select2 with Icons ──────────────────────────────────────
const initIconPicker = () => {
    // ─── نختار كل الـ Selects اللي عليها الكلاس ──────────────
    document.querySelectorAll('.icon-picker-select').forEach((select) => {
        // ─── نتأكد إنه ما تمش تفعيله قبل كده ──────────────────
        if (select.dataset.select2Initialized === 'true') return;
        select.dataset.select2Initialized = 'true';
        
        // ─── تفعيل Select2 ──────────────────────────────────────
        $(select).select2({
            dropdownParent: select.closest('.modal') || document.body,
            templateResult: function(option) {
                if (!option.id) return option.text;
                
                const iconClass = option.element?.dataset?.icon || option.id;
                return $(
                    `<span><i class="${iconClass}" style="margin-right:10px;font-size:18px;width:28px;display:inline-block;text-align:center;"></i> ${option.text}</span>`
                );
            },
            templateSelection: function(option) {
                if (!option.id) return option.text;
                
                const iconClass = option.element?.dataset?.icon || option.id;
                return $(
                    `<span><i class="${iconClass}" style="margin-right:8px;font-size:16px;"></i> ${option.text}</span>`
                );
            },
            allowClear: true,
            placeholder: "{{ trans('dashboard/menus.select_icon') }}",
        });
        
        // ─── عند تغيير القيمة، نحدث الـ Preview ─────────────────
        $(select).on('change', function() {
            const previewSelector = this.dataset.preview;
            if (!previewSelector) return;
            
            const preview = document.querySelector(previewSelector);
            if (!preview) return;
            
            const selectedOption = this.options[this.selectedIndex];
            const iconClass = selectedOption?.value || '';
            
            if (iconClass) {
                preview.innerHTML = `<i class="${iconClass}"></i>`;
            } else {
                preview.innerHTML = `<i class="ti ti-users"></i>`;
            }
        });
    });
};

// ─── تحديث قيمة Select2 برمجياً ───────────────────────────────────────────
const setIconPickerValue = (selectId, previewId, iconValue) => {
    const select = document.getElementById(selectId);
    const preview = document.getElementById(previewId);
    
    if (!select) return;
    
    // ─── نحدد القيمة ────────────────────────────────────────────
    if (iconValue) {
        $(select).val(iconValue).trigger('change');
        if (preview) {
            preview.innerHTML = `<i class="${iconValue}"></i>`;
        }
    } else {
        $(select).val('').trigger('change');
        if (preview) {
            preview.innerHTML = `<i class="ti ti-users"></i>`;
        }
    }
};

// ─── Init ─────────────────────────────────────────────────────────────────
const init = () => {
    initToggleStatus();
    initEdit();
    initDelete();
    initRestore();
    initForceDelete();
    initIconPicker();
};

window.Menus = {
    init: init,
    reloadTable: reloadTable,
    fetchJson: fetchJson,
    parseJson: parseJson,
    setBtnLoading: setBtnLoading,
    showConfirmModal: showConfirmModal,
    getModal: getModal,
    hideModal: hideModal,
};
