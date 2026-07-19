/**
 * ============================================================
 * 📁 FILE: bulk_actions.js
 * 📌 MAIN FUNCTIONS: Bulk Actions for Vendors
 * ============================================================
 */

"use strict";

// ─── Bulk Actions Global Variables ──────────────────────────────────────────
var bulkActionData = null;
var bulkActionType = null;

// ─── دالة جلب أسماء الموردين المختارة ──────────────────
function getSelectedVendorNames(checkboxes) {
    const names = [];
    checkboxes.forEach((cb) => {
        const row = cb.closest("tr");
        const nameCell = row?.querySelector("td:nth-child(2)"); // ─── العمود الثاني هو الاسم ───
        if (nameCell) {
            names.push(nameCell.textContent.trim());
        }
    });
    return names
        .map(
            (name) =>
                `<span class="badge bg-secondary me-1 mb-1 p-2">${name}</span>`,
        )
        .join("");
}

// ─── Bulk Status Modal ──────────────────────────────────────
function openBulkStatusModal() {
    const checkboxes = document.querySelectorAll(".row-checkbox:checked");
    const ids = Array.from(checkboxes).map((cb) => parseInt(cb.value));

    if (ids.length === 0) {
        Alert.warning(window.translations.bulk_select_at_least_one);
        return;
    }

    const vendorNames = getSelectedVendorNames(checkboxes);

    const titleEl = document.getElementById("bulkActionModalTitle");
    const messageEl = document.getElementById("bulkActionMessage");
    const statusDropdown = document.getElementById("bulkStatusDropdown");
    const confirmBtn = document.getElementById("confirmBulkAction");

    // ─── نتحقق من وجود العناصر قبل التعامل معها ──────────────
    if (titleEl) titleEl.textContent = window.translations.bulk_change_status;
    if (messageEl)
        messageEl.textContent = window.translations.bulk_status_confirm;
    if (statusDropdown) statusDropdown.style.display = "block";

    // ─── عرض أسماء الموردين في الرسالة ──────────────────────
    if (messageEl && vendorNames) {
        messageEl.innerHTML =
            window.translations.bulk_status_confirm + "<br><br>" + vendorNames;
    }

    bulkActionData = { ids: ids };
    bulkActionType = "status";

    if (confirmBtn) {
        confirmBtn.className = "btn btn-success";
        const label = confirmBtn.querySelector(".indicator-label");
        if (label) label.textContent = window.translations.confirm;
    }

    const modal = new bootstrap.Modal(
        document.getElementById("bulkActionModal"),
    );
    modal.show();
}

// ─── Bulk Delete Modal ──────────────────────────────────────
function openBulkDeleteModal() {
    const checkboxes = document.querySelectorAll(".row-checkbox:checked");
    const ids = Array.from(checkboxes).map((cb) => parseInt(cb.value));

    if (ids.length === 0) {
        Alert.warning(window.translations.bulk_select_at_least_one);
        return;
    }

    const vendorNames = getSelectedVendorNames(checkboxes);

    const titleEl = document.getElementById("bulkActionModalTitle");
    const messageEl = document.getElementById("bulkActionMessage");
    const statusDropdown = document.getElementById("bulkStatusDropdown");
    const confirmBtn = document.getElementById("confirmBulkAction");

    if (titleEl) titleEl.textContent = window.translations.delete_selected;
    if (messageEl) {
        messageEl.innerHTML =
            window.translations.bulk_delete_confirm + "<br><br>" + vendorNames;
    }
    if (statusDropdown) statusDropdown.style.display = "none";

    bulkActionData = { ids: ids };
    bulkActionType = "delete";

    if (confirmBtn) {
        confirmBtn.className = "btn btn-danger";
        const label = confirmBtn.querySelector(".indicator-label");
        if (label) label.textContent = window.translations.delete;
    }

    const modal = new bootstrap.Modal(
        document.getElementById("bulkActionModal"),
    );
    modal.show();
}

// ─── Bulk Restore Modal ──────────────────────────────────────
function openBulkRestoreModal() {
    const checkboxes = document.querySelectorAll(".row-checkbox:checked");
    const ids = Array.from(checkboxes).map((cb) => parseInt(cb.value));

    if (ids.length === 0) {
        Alert.warning(window.translations.bulk_select_at_least_one);
        return;
    }

    const vendorNames = getSelectedVendorNames(checkboxes);

    const titleEl = document.getElementById("bulkActionModalTitle");
    const messageEl = document.getElementById("bulkActionMessage");
    const statusDropdown = document.getElementById("bulkStatusDropdown");
    const confirmBtn = document.getElementById("confirmBulkAction");

    if (titleEl)
        titleEl.textContent =
            window.translations.bulk_restore || "استعادة جماعية";
    if (messageEl) {
        messageEl.innerHTML =
            (window.translations.bulk_restore_confirm ||
                "هل أنت متأكد من استعادة الموردين المختارين؟") +
            "<br><br>" +
            vendorNames;
    }
    if (statusDropdown) statusDropdown.style.display = "none";

    bulkActionData = { ids: ids };
    bulkActionType = "restore";

    if (confirmBtn) {
        confirmBtn.className = "btn btn-success";
        const label = confirmBtn.querySelector(".indicator-label");
        if (label) label.textContent = window.translations.restore || "استعادة";
    }

    const modal = new bootstrap.Modal(
        document.getElementById("bulkActionModal"),
    );
    modal.show();
}

// ─── Bulk Force Delete Modal ──────────────────────────────────
function openBulkForceDeleteModal() {
    const checkboxes = document.querySelectorAll(".row-checkbox:checked");
    const ids = Array.from(checkboxes).map((cb) => parseInt(cb.value));

    if (ids.length === 0) {
        Alert.warning(window.translations.bulk_select_at_least_one);
        return;
    }

    const vendorNames = getSelectedVendorNames(checkboxes);

    const titleEl = document.getElementById("bulkActionModalTitle");
    const messageEl = document.getElementById("bulkActionMessage");
    const statusDropdown = document.getElementById("bulkStatusDropdown");
    const confirmBtn = document.getElementById("confirmBulkAction");

    if (titleEl)
        titleEl.textContent =
            window.translations.bulk_force_delete || "حذف نهائي جماعي";
    if (messageEl) {
        messageEl.innerHTML =
            (window.translations.bulk_force_delete_confirm ||
                "تحذير! هذا الإجراء لا يمكن التراجع عنه. هل أنت متأكد؟") +
            "<br><br>" +
            vendorNames;
    }
    if (statusDropdown) statusDropdown.style.display = "none";

    bulkActionData = { ids: ids };
    bulkActionType = "force_delete";

    if (confirmBtn) {
        confirmBtn.className = "btn btn-danger";
        const label = confirmBtn.querySelector(".indicator-label");
        if (label)
            label.textContent = window.translations.force_delete || "حذف نهائي";
    }

    const modal = new bootstrap.Modal(
        document.getElementById("bulkActionModal"),
    );
    modal.show();
}

// ─── تأكيد الـ Bulk Action ──────────────────────────────────────────────
document.addEventListener("click", async function (e) {
    const confirmBtn = e.target.closest("#confirmBulkAction");
    if (!confirmBtn) return;
    if (!bulkActionData || !bulkActionType) return;

    const { ids } = bulkActionData;
    const indicatorLabel = confirmBtn.querySelector(".indicator-label");
    const indicatorProgress = confirmBtn.querySelector(".indicator-progress");

    if (indicatorLabel) indicatorLabel.classList.add("d-none");
    if (indicatorProgress) indicatorProgress.classList.remove("d-none");
    confirmBtn.disabled = true;

    try {
        let payload = { ids: ids };

        switch (bulkActionType) {
            case "status":
                payload.action = "status";
                const statusSelect =
                    document.getElementById("bulkStatusSelect");
                if (statusSelect) {
                    payload.status = statusSelect.value;
                }
                break;

            case "delete":
                payload.action = "delete";
                break;

            case "restore":
                payload.action = "restore";
                break;

            case "force_delete":
                payload.action = "force_delete";
                break;

            default:
                Alert.error(window.translations.error);
                return;
        }

        const response = await fetch(window.routes.bulkAction, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                Accept: "application/json",
                "X-CSRF-TOKEN": document.querySelector(
                    'meta[name="csrf-token"]',
                ).content,
                "X-Requested-With": "XMLHttpRequest",
            },
            body: JSON.stringify(payload),
        });

        const data = await response.json();

        if (data?.success) {
            Alert.success(data.message);

            // ─── إعادة تحميل الـ Table ──────────────────────────
            if (
                window.LaravelDataTables &&
                window.LaravelDataTables["vendors_datatable"]
            ) {
                window.LaravelDataTables["vendors_datatable"].ajax.reload(
                    null,
                    false,
                );
            }

            // ─── التحقق من وجود محذوفين ──────────────────────────
            if (
                ["delete", "restore", "force_delete"].includes(bulkActionType)
            ) {
                setTimeout(window.checkTrashed, 500);
            }

            // ─── إلغاء تحديد الكل ────────────────────────────────
            document
                .querySelectorAll(".row-checkbox")
                .forEach((cb) => (cb.checked = false));
            const selectAll = document.getElementById("selectAllCheckbox");
            if (selectAll) selectAll.checked = false;

            const bulkBtn = document.getElementById("bulkActionsBtn");
            if (bulkBtn) bulkBtn.style.display = "none";

            const modal = bootstrap.Modal.getInstance(
                document.getElementById("bulkActionModal"),
            );
            if (modal) modal.hide();
        } else {
            Alert.error(data?.message || window.translations.error);
        }
    } catch (err) {
        console.error("Bulk Action Error:", err);
        Alert.error(window.translations.error);
    } finally {
        if (indicatorLabel) indicatorLabel.classList.remove("d-none");
        if (indicatorProgress) indicatorProgress.classList.add("d-none");
        confirmBtn.disabled = false;
        bulkActionData = null;
        bulkActionType = null;
    }
});

// ─── عند إغلاق الـ Modal، امسح البيانات ──────────────────────────────
document.addEventListener("hidden.bs.modal", function (e) {
    if (e.target.id === "bulkActionModal") {
        bulkActionData = null;
        bulkActionType = null;
    }
});

// ─── Export functions to global window ──────────────────────────────────────
window.openBulkStatusModal = openBulkStatusModal;
window.openBulkDeleteModal = openBulkDeleteModal;
window.openBulkRestoreModal = openBulkRestoreModal;
window.openBulkForceDeleteModal = openBulkForceDeleteModal;
