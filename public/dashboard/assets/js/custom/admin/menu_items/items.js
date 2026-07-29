"use strict";

document.addEventListener("DOMContentLoaded", function () {
    const P = window.ItemPanel;

    // ─── عناصر المودال (نتأكد إنها موجودة قبل ما نستخدمها) ────────
    const itemModalEl = document.getElementById("panelItemModal");
    const confirmModalEl = document.getElementById("panelConfirmModal");

    if (!itemModalEl || !confirmModalEl) {
        console.error("Panel modals not found in DOM. Check that item-modal.blade.php and confirm-modals.blade.php are included INSIDE @section('content') before @endsection.");
        return;
    }

    // ─── Icon Picker (select2) ─────────────────────────────────
    const initIconPicker = () => {
        const select = document.getElementById("panel_icon");
        if (!select || select.dataset.select2Initialized === "true") return;
        select.dataset.select2Initialized = "true";

        $(select).select2({
            dropdownParent: $(select).closest(".modal"),
            templateResult: function (option) {
                if (!option.id) return option.text;
                return $(`<span><i class="${option.id}" style="margin-right:10px;font-size:18px;width:28px;display:inline-block;text-align:center;"></i> ${option.text}</span>`);
            },
            templateSelection: function (option) {
                if (!option.id) return option.text;
                return $(`<span><i class="${option.id}" style="margin-right:8px;"></i> ${option.text}</span>`);
            },
            allowClear: true,
            placeholder: window.panelTranslations?.select_icon || "اختر أيقونة",
        });

        $(select).on("change", function () {
            const preview = document.querySelector("#panel_icon_preview");
            const val = $(select).val();
            if (preview) preview.innerHTML = `<i class="${val || 'ti ti-users'}"></i>`;
        });
    };

    const setIconValue = (value) => {
        const select = $("#panel_icon");
        select.val(value || "").trigger("change");
    };

    // ─── فتح المودال في وضع الإنشاء ──────────────────────────────
    const openCreateModal = () => {
        const form = document.getElementById("panelItemForm");
        form.reset();
        document.getElementById("panel_item_id").value = "";
        document.getElementById("panelItemModalTitle").textContent = window.panelTranslations.create_label;
        setIconValue("");
        document.getElementById("panelItemModalLoader").classList.add("d-none");
        form.classList.remove("d-none");

        const modal = bootstrap.Modal.getOrCreateInstance(itemModalEl);
        modal.show();
    };

    // ─── فتح المودال في وضع التعديل ──────────────────────────────
    const openEditModal = async (id) => {
        const modal = bootstrap.Modal.getOrCreateInstance(itemModalEl);
        const form = document.getElementById("panelItemForm");
        const loader = document.getElementById("panelItemModalLoader");

        document.getElementById("panelItemModalTitle").textContent = window.panelTranslations.edit_label;
        form.classList.add("d-none");
        loader.classList.remove("d-none");
        modal.show();

        try {
            const res = await P.fetchJson(P.route("edit", id));
            if (!res?.success) {
                Alert.error(window.panelTranslations.error);
                modal.hide();
                return;
            }

            const data = res.data;
            document.getElementById("panel_item_id").value = data.id;
            document.getElementById("panel_type").value = data.type ?? "link";
            document.getElementById("panel_link_type").value = data.link_type ?? "none";
            document.getElementById("panel_route_name").value = data.route_name ?? "";
            document.getElementById("panel_url").value = data.url ?? "";
            document.getElementById("panel_target").value = data.target ?? "_self";
            document.getElementById("panel_status").value = data.status ?? "active";
            document.getElementById("panel_is_owner_only").value = data.is_owner_only ? "1" : "0";
            document.getElementById("panel_permission_name").value = data.permission_name ?? "";
            document.getElementById("panel_badge_text").value = data.badge_text ?? "";
            document.getElementById("panel_badge_color").value = data.badge_color ?? "";

            const visibleFrom = document.getElementById("panel_visible_from");
            if (visibleFrom) visibleFrom.value = data.visible_from ? data.visible_from.replace(" ", "T") : "";

            const visibleUntil = document.getElementById("panel_visible_until");
            if (visibleUntil) visibleUntil.value = data.visible_until ? data.visible_until.replace(" ", "T") : "";

            const companySelect = document.getElementById("panel_company_id");
            if (companySelect) companySelect.value = data.company_id ?? "";

            setIconValue(data.icon ?? "");

            const translations = data.translations || [];
            document.querySelectorAll('#panelItemForm [name^="locales["]').forEach((input) => {
                const match = input.name.match(/locales\[([^\]]+)\]\[([^\]]+)\]/);
                if (!match) return;
                const [, locale, field] = match;
                const t = translations.find((tr) => tr.locale === locale);
                input.value = t ? (t[field] ?? "") : "";
            });

            loader.classList.add("d-none");
            form.classList.remove("d-none");
        } catch (err) {
            console.error(err);
            Alert.error(window.panelTranslations.error);
            modal.hide();
        }
    };

    // ─── حفظ (إنشاء أو تعديل) ─────────────────────────────────────
    const saveItem = async () => {
        const saveBtn = document.getElementById("panelItemSaveBtn");
        const form = document.getElementById("panelItemForm");
        const id = document.getElementById("panel_item_id").value;
        const isEdit = !!id;

        const formData = new FormData();
        if (isEdit) formData.append("_method", "PUT");
        formData.append("_token", document.querySelector('meta[name="csrf-token"]').content);

        const fields = ["type", "icon", "link_type", "route_name", "url", "target", "status", "is_owner_only", "permission_name", "badge_text", "badge_color", "visible_from", "visible_until", "company_id"];
        fields.forEach((f) => {
            const input = form.querySelector(`[name="${f}"]`);
            if (input) formData.append(f, input.value ?? "");
        });

        form.querySelectorAll('[name^="locales["]').forEach((input) => {
            formData.append(input.name, input.value ?? "");
        });

        const indicatorLabel = saveBtn.querySelector(".indicator-label");
        const indicatorProgress = saveBtn.querySelector(".indicator-progress");
        indicatorLabel.classList.add("d-none");
        indicatorProgress.classList.remove("d-none");
        saveBtn.disabled = true;

        try {
            const url = isEdit ? P.route("update", id) : window.panelRoutes.store;
            const res = await fetch(url, {
                method: "POST",
                headers: {
                    Accept: "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                },
                body: formData,
            });
            const data = await res.json();

            if (data?.success) {
                Alert.success(data.message);
                bootstrap.Modal.getInstance(itemModalEl)?.hide();
                P.loadPage(true);
            } else {
                Alert.error(data?.message || window.panelTranslations.error);
            }
        } catch (err) {
            console.error(err);
            Alert.error(window.panelTranslations.error);
        } finally {
            indicatorLabel.classList.remove("d-none");
            indicatorProgress.classList.add("d-none");
            saveBtn.disabled = false;
        }
    };

    // ─── حذف عنصر مفرد (Modal) ────────────────────────────────────
    let pendingDeleteId = null;
    const confirmModal = bootstrap.Modal.getOrCreateInstance(confirmModalEl);
    const confirmTitle = document.getElementById("panelConfirmTitle");
    const confirmBody = document.getElementById("panelConfirmBody");
    const confirmBtn = document.getElementById("panelConfirmBtn");

    let confirmAction = null;

    const openConfirm = (title, body, action) => {
        confirmTitle.textContent = title;
        confirmBody.textContent = body;
        confirmAction = action;
        confirmModal.show();
    };

    confirmBtn.addEventListener("click", async () => {
        if (typeof confirmAction !== "function") return;

        const indicatorLabel = confirmBtn.querySelector(".indicator-label");
        const indicatorProgress = confirmBtn.querySelector(".indicator-progress");
        indicatorLabel.classList.add("d-none");
        indicatorProgress.classList.remove("d-none");
        confirmBtn.disabled = true;

        await confirmAction();

        indicatorLabel.classList.remove("d-none");
        indicatorProgress.classList.add("d-none");
        confirmBtn.disabled = false;
        confirmModal.hide();
        confirmAction = null;
    });

    // ─── Event Delegation ──────────────────────────────────────
    document.addEventListener("click", (e) => {
        if (e.target.closest("#btnCreatePanelItem")) {
            openCreateModal();
        }

        if (e.target.closest("#panelItemSaveBtn")) {
            saveItem();
        }

        const editBtn = e.target.closest(".btn-item-edit");
        if (editBtn) {
            openEditModal(editBtn.dataset.id);
        }

        const deleteBtn = e.target.closest(".btn-item-delete");
        if (deleteBtn) {
            pendingDeleteId = deleteBtn.dataset.id;
            const li = deleteBtn.closest(".available-item");
            const itemTitle = li?.querySelector(".node-title")?.textContent?.trim() || "";
            const msg = (window.panelTranslations.confirm_delete || "").replace(":title", itemTitle);

            openConfirm(
                window.panelTranslations.delete_label,
                msg,
                async () => {
                    try {
                        const res = await P.fetchJson(P.route("destroy", pendingDeleteId), "DELETE", {});
                        if (res.success) {
                            Alert.success(res.message);
                            document.querySelector(`.available-item[data-item-id="${pendingDeleteId}"]`)?.remove();
                            P.checkHasTrashed();
                        } else {
                            Alert.error(res.message || window.panelTranslations.error);
                        }
                    } catch (err) {
                        console.error(err);
                        Alert.error(window.panelTranslations.error);
                    }
                }
            );
        }
    });

    // ─── تبديل الحالة (status) ─────────────────────────────────
    document.addEventListener("change", async (e) => {
        const cb = e.target.closest(".toggle-item-status");
        if (!cb) return;

        try {
            const res = await P.fetchJson(P.route("toggleStatus", cb.dataset.id), "PATCH", {});
            if (!res.success) {
                cb.checked = !cb.checked;
                Alert.error(res.message || window.panelTranslations.error);
            }
        } catch (err) {
            cb.checked = !cb.checked;
            console.error(err);
            Alert.error(window.panelTranslations.error);
        }
    });

    // ─── تبديل is_owner_only ─────────────────────────────────────
    document.addEventListener("change", async (e) => {
        const cb = e.target.closest(".toggle-item-owner");
        if (!cb) return;

        try {
            const res = await P.fetchJson(P.route("toggleOwner", cb.dataset.id), "PATCH", {});
            if (!res.success) {
                cb.checked = !cb.checked;
                Alert.error(res.message || window.panelTranslations.error);
            }
        } catch (err) {
            cb.checked = !cb.checked;
            console.error(err);
            Alert.error(window.panelTranslations.error);
        }
    });

    // ─── تفعيل select2 عند فتح المودال ────────────────────────────
    document.addEventListener("shown.bs.modal", (e) => {
        if (e.target.id !== "panelItemModal") return;
        initIconPicker();
    });

    document.addEventListener("hidden.bs.modal", (e) => {
        if (e.target.id !== "panelItemModal") return;
        setIconValue("");
    });
});
