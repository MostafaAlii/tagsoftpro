"use strict";

document.addEventListener("DOMContentLoaded", function () {
    const P = window.ItemPanel;

    const confirmModalEl = document.getElementById("panelConfirmModal");
    const bulkStatusModalEl = document.getElementById("panelBulkStatusModal");

    if (!confirmModalEl || !bulkStatusModalEl) {
        console.error("Trashed/bulk modals not found in DOM. Check confirm-modals.blade.php is included INSIDE @section('content') before @endsection.");
        return;
    }

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

    // ─── استعادة / حذف نهائي (مفرد) ────────────────────────────
    document.addEventListener("click", (e) => {
        const restoreBtn = e.target.closest(".btn-item-restore");
        if (restoreBtn) {
            const id = restoreBtn.dataset.id;
            openConfirm(
                window.panelTranslations.restore_label,
                window.panelTranslations.confirm_restore,
                async () => {
                    try {
                        const res = await P.fetchJson(P.route("restore", id), "PATCH", {});
                        if (res.success) {
                            Alert.success(res.message);
                            document.querySelector(`.available-item[data-item-id="${id}"]`)?.remove();
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

        const forceDeleteBtn = e.target.closest(".btn-item-force-delete");
        if (forceDeleteBtn) {
            const id = forceDeleteBtn.dataset.id;
            openConfirm(
                window.panelTranslations.force_delete_label,
                window.panelTranslations.confirm_force_delete,
                async () => {
                    try {
                        const res = await P.fetchJson(P.route("forceDelete", id), "DELETE", {});
                        if (res.success) {
                            Alert.success(res.message);
                            document.querySelector(`.available-item[data-item-id="${id}"]`)?.remove();
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

    // ─── حذف جماعي (Active mode) ──────────────────────────────────
    document.getElementById("btnBulkDelete").addEventListener("click", () => {
        if (P.state.selectedIds.size === 0) {
            Alert.error(window.panelTranslations.select_at_least_one);
            return;
        }
        openConfirm(
            window.panelTranslations.delete_selected_label,
            window.panelTranslations.confirm_bulk_delete,
            () => runBulkAction("delete")
        );
    });

    // ─── استرجاع جماعي (Trashed mode) ─────────────────────────────
    document.getElementById("btnBulkRestore").addEventListener("click", () => {
        if (P.state.selectedIds.size === 0) {
            Alert.error(window.panelTranslations.select_at_least_one);
            return;
        }
        openConfirm(
            window.panelTranslations.bulk_restore_label,
            window.panelTranslations.confirm_bulk_restore,
            () => runBulkAction("restore")
        );
    });

    // ─── حذف نهائي جماعي (Trashed mode) ────────────────────────────
    document.getElementById("btnBulkForceDelete").addEventListener("click", () => {
        if (P.state.selectedIds.size === 0) {
            Alert.error(window.panelTranslations.select_at_least_one);
            return;
        }
        openConfirm(
            window.panelTranslations.bulk_force_delete_label,
            window.panelTranslations.confirm_bulk_force_delete,
            () => runBulkAction("force_delete")
        );
    });

    const runBulkAction = async (action, extraParams = {}) => {
        try {
            const res = await P.fetchJson(window.panelRoutes.bulkAction, "POST", {
                action,
                ids: Array.from(P.state.selectedIds),
                ...extraParams,
            });

            if (res.success) {
                Alert.success(res.message);
                P.loadPage(true);
                P.checkHasTrashed();
            } else {
                Alert.error(res.message || window.panelTranslations.error);
            }
        } catch (err) {
            console.error(err);
            Alert.error(window.panelTranslations.error);
        }
    };

    // ─── تعديل حالة جماعي ──────────────────────────────────────────
    const bulkStatusModal = bootstrap.Modal.getOrCreateInstance(bulkStatusModalEl);
    const bulkStatusItemsList = document.getElementById("panelBulkStatusItemsList");
    const bulkStatusSelect = document.getElementById("panelBulkStatusSelect");
    const bulkStatusConfirmBtn = document.getElementById("panelBulkStatusConfirmBtn");

    document.getElementById("btnBulkChangeStatus").addEventListener("click", () => {
        if (P.state.selectedIds.size === 0) {
            Alert.error(window.panelTranslations.select_at_least_one);
            return;
        }

        bulkStatusItemsList.innerHTML = "";
        P.state.selectedIds.forEach((id) => {
            const li = document.querySelector(`.available-item[data-item-id="${id}"]`);
            const title = li?.querySelector(".node-title")?.textContent || `#${id}`;
            const itemEl = document.createElement("li");
            itemEl.textContent = title;
            bulkStatusItemsList.appendChild(itemEl);
        });

        bulkStatusModal.show();
    });

    bulkStatusConfirmBtn.addEventListener("click", async () => {
        const indicatorLabel = bulkStatusConfirmBtn.querySelector(".indicator-label");
        const indicatorProgress = bulkStatusConfirmBtn.querySelector(".indicator-progress");
        indicatorLabel.classList.add("d-none");
        indicatorProgress.classList.remove("d-none");
        bulkStatusConfirmBtn.disabled = true;

        await runBulkAction("change_status", { status: bulkStatusSelect.value });

        indicatorLabel.classList.remove("d-none");
        indicatorProgress.classList.add("d-none");
        bulkStatusConfirmBtn.disabled = false;
        bulkStatusModal.hide();
    });
});
