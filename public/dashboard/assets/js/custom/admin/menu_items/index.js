/**
 * ============================================================
 * 📁 FILE: index.js
 * 📌 MAIN FUNCTIONS: Menu Items CRUD Operations
 * ============================================================
 */

"use strict";

// ─── Helpers ──────────────────────────────────────────────────────────────
const route = (name, id) => window.routes[name].replace("__ID__", id);

const csrfToken = () =>
    document.querySelector('meta[name="csrf-token"]').content;

const reloadTable = () => {
    try {
        window.LaravelDataTables["menu_items_datatable"].ajax.reload(
            null,
            false,
        );
    } catch (e) {
        console.warn("reloadTable fallback:", e);
        $("#menu_items_datatable").DataTable().ajax.reload(null, false);
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
    const form = document.getElementById("editMenuItemForm");
    
    if (loader) {
        loader.classList.toggle("d-none", !show);
    }
    if (form) {
        form.classList.toggle("d-none", show);
    }
};

const populateEditForm = (data) => {
    if (!data) {
        console.error('No data received');
        return;
    }

    console.log('Populating edit form with:', data);

    // ─── الحقول الأساسية ──────────────────────────────────────
    const itemId = document.getElementById("editMenuItemId");
    if (itemId) itemId.value = data.id;

    const typeSelect = document.getElementById("edit_type");
    if (typeSelect) typeSelect.value = data.type ?? "link";

    setIconPickerValue('edit_icon', 'edit_icon_preview', data.icon ?? '');

    const linkTypeSelect = document.getElementById("edit_link_type");
    if (linkTypeSelect) linkTypeSelect.value = data.link_type ?? "none";

    const routeNameInput = document.getElementById("edit_route_name");
    if (routeNameInput) routeNameInput.value = data.route_name ?? "";

    const urlInput = document.getElementById("edit_url");
    if (urlInput) urlInput.value = data.url ?? "";

    const targetSelect = document.getElementById("edit_target");
    if (targetSelect) targetSelect.value = data.target ?? "_self";

    const statusSelect = document.getElementById("edit_status");
    if (statusSelect) statusSelect.value = data.status ?? "active";

    const ownerOnlySelect = document.getElementById("edit_is_owner_only");
    if (ownerOnlySelect) ownerOnlySelect.value = data.is_owner_only ? 1 : 0;

    const permissionInput = document.getElementById("edit_permission_name");
    if (permissionInput) permissionInput.value = data.permission_name ?? "";

    const badgeTextInput = document.getElementById("edit_badge_text");
    if (badgeTextInput) badgeTextInput.value = data.badge_text ?? "";

    const badgeColorSelect = document.getElementById("edit_badge_color");
    if (badgeColorSelect) badgeColorSelect.value = data.badge_color ?? "";

    const visibleFromInput = document.getElementById("edit_visible_from");
    if (visibleFromInput && data.visible_from) {
        visibleFromInput.value = data.visible_from.replace(' ', 'T');
    }

    const visibleUntilInput = document.getElementById("edit_visible_until");
    if (visibleUntilInput && data.visible_until) {
        visibleUntilInput.value = data.visible_until.replace(' ', 'T');
    }

    const companySelect = document.getElementById("edit_company_id");
    if (companySelect) {
        companySelect.value = data.company_id ?? "";
    }

    // ─── الترجمات ──────────────────────────────────────────────
    const translations = data.translations || [];
    const localeInputs = document.querySelectorAll('[name^="locales["]');
    localeInputs.forEach((input) => {
        const match = input.name.match(/locales\[([^\]]+)\]\[([^\]]+)\]/);
        if (match) {
            const locale = match[1];
            const field = match[2];
            const translation = translations.find((t) => t.locale === locale);
            if (translation) {
                input.value = translation[field] ?? "";
            } else {
                input.value = "";
            }
        }
    });
};

// ─── Icon Picker - Select2 with Icons ──────────────────────────────────────
const initIconPicker = () => {
    document.querySelectorAll('.icon-picker-select').forEach((select) => {
        if (select.dataset.select2Initialized === 'true') return;
        select.dataset.select2Initialized = 'true';

        $(select).select2({
            dropdownParent: select.closest('.modal') || document.body,
            templateResult: function (option) {
                if (!option.id) return option.text;
                const iconClass = option.id;
                return $(
                    `<span><i class="${iconClass}" style="margin-right:10px;font-size:18px;width:28px;display:inline-block;text-align:center;"></i> ${option.text}</span>`
                );
            },
            templateSelection: function (option) {
                if (!option.id) return option.text;
                const iconClass = option.id;
                return $(
                    `<span><i class="${iconClass}" style="margin-right:8px;font-size:16px;"></i> ${option.text}</span>`
                );
            },
            allowClear: true,
            placeholder: window.translations?.select_icon || "اختر أيقونة",
        });

        $(select).on('change', function () {
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

    if (iconValue) {
        let matchedOption = Array.from(select.options).find(
            (opt) => opt.value === iconValue
        );

        if (!matchedOption) {
            matchedOption = Array.from(select.options).find(
                (opt) => opt.value.endsWith(iconValue) || iconValue.endsWith(opt.value)
            );
        }

        const finalValue = matchedOption ? matchedOption.value : iconValue;

        $(select).val(finalValue).trigger('change');

        if (preview) {
            preview.innerHTML = `<i class="${finalValue}"></i>`;
        }
    } else {
        $(select).val('').trigger('change');
        if (preview) {
            preview.innerHTML = `<i class="ti ti-users"></i>`;
        }
    }
};

const initEdit = () => {
    document.addEventListener("click", async (e) => {
        const btn = e.target.closest(".btn-edit");
        if (!btn) return;

        getModal("editMenuItemModal").show();
        showEditLoader(true);

        try {
            const res = await fetchJson(route("edit", btn.dataset.id), "GET");
            const data = await parseJson(res);

            if (data?.success) {
                populateEditForm(data.data);
                showEditLoader(false);
            } else {
                hideModal("editMenuItemModal");
                Alert.error(window.translations.error);
            }
        } catch (err) {
            console.error(err);
            hideModal("editMenuItemModal");
            Alert.error(window.translations.error);
        }
    });

    // ─── حفظ التعديل ──────────────────────────────────────────
    document.addEventListener("click", async (e) => {
        const saveBtn = e.target.closest("#saveEditMenuItem");
        if (!saveBtn) return;

        const id = document.getElementById("editMenuItemId")?.value;
        const form = document.getElementById("editMenuItemForm");
        
        if (!id) {
            Alert.error(window.translations.error);
            return;
        }

        const formData = new FormData();
        formData.append("_method", "PUT");
        formData.append("_token", csrfToken());

        // ─── الحقول الأساسية ──────────────────────────────────────
        const fields = ["type", "icon", "link_type", "route_name", "url", "target", "status", "is_owner_only", "permission_name", "badge_text", "badge_color", "visible_from", "visible_until"];
        fields.forEach((field) => {
            const input = form.querySelector(`[name="${field}"]`);
            if (input) {
                formData.append(field, input.value ?? "");
            }
        });

        // ─── الشركة ──────────────────────────────────────────────────
        const companyInput = form.querySelector('[name="company_id"]');
        if (companyInput) {
            formData.append("company_id", companyInput.value ?? "");
        }

        // ─── الترجمات ──────────────────────────────────────────────
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
                hideModal("editMenuItemModal");
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

    // ─── عند إغلاق المودال ──────────────────────────────────────────
    document.addEventListener("hidden.bs.modal", (e) => {
        if (e.target.id !== "editMenuItemModal") return;
        const form = document.getElementById("editMenuItemForm");
        if (form) form.reset();
        showEditLoader(true);
        const iconSelect = document.getElementById("edit_icon");
        if (iconSelect && $(iconSelect).data('select2')) {
            $(iconSelect).val('').trigger('change');
        }
    });
};

// ─── Delete ───────────────────────────────────────────────────────────────
const initDelete = () => {
    document.addEventListener("click", (e) => {
        const btn = e.target.closest(".btn-delete");
        if (!btn) return;

        const row = btn.closest("tr");
        const nameCell = row?.querySelector("td:nth-child(3)");
        const itemTitle = nameCell?.textContent?.trim() || '';

        const confirmMessage = document.querySelector("#deleteMenuItemModal .modal-body p");
        if (confirmMessage) {
            confirmMessage.textContent = window.translations.delete_confirm?.replace(':title', itemTitle)
                || `هل أنت متأكد من حذف عنصر القائمة "${itemTitle}"؟`;
        }

        el("deleteMenuItemId").value = btn.dataset.id;
        getModal("deleteMenuItemModal").show();
    });

    document.addEventListener("click", async (e) => {
        if (!e.target.closest("#confirmDeleteMenuItem")) return;

        const confirmBtn = el("confirmDeleteMenuItem");
        const id = el("deleteMenuItemId").value;
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
                hideModal("deleteMenuItemModal");
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
        if (e.target.id !== "deleteMenuItemModal") return;
        el("deleteMenuItemId").value = "";
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
                "هل أنت متأكد من استعادة هذا العنصر؟",
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
            }
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
            }
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
    initIconPicker();
};

// ─── تعريف MenuItems كـ object عالمي ──────────────────────────────────────
window.MenuItems = {
    init: init,
    reloadTable: reloadTable,
    fetchJson: fetchJson,
    parseJson: parseJson,
    setBtnLoading: setBtnLoading,
    showConfirmModal: showConfirmModal,
    getModal: getModal,
    hideModal: hideModal,
    initIconPicker: initIconPicker,
};