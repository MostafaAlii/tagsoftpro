"use strict";

// ─── Bulk Actions Global Variables ──────────────────────────────────────────

var bulkActionData = null;
var bulkActionType = null;

function getSelectedEmployeeNames(checkboxes) {
    const names = [];
    checkboxes.forEach((cb) => {
        const row = cb.closest("tr");
        const nameCell = row?.querySelector("td:nth-child(4)");
        if (nameCell) {
            names.push(nameCell.textContent.trim());
        }
    });
    return names.map((name) =>
        `<span class="badge bg-secondary me-1 mb-1 p-2">${name}</span>`
    ).join("");
}

function openBulkStatusModal() {
    const checkboxes = document.querySelectorAll(".row-checkbox:checked");
    const ids = Array.from(checkboxes).map((cb) => parseInt(cb.value));

    if (ids.length === 0) {
        Alert.warning(window.translations.bulk_select_at_least_one);
        return;
    }

    const employeeNames = getSelectedEmployeeNames(checkboxes);

    document.getElementById("bulkActionModalTitle").textContent = window.translations.bulk_change_status;
    document.getElementById("bulkActionMessage").textContent = window.translations.bulk_status_confirm;
    document.getElementById("bulkStatusDropdown").style.display = "block";
    document.getElementById("bulkActionEmployeeList").innerHTML = employeeNames;

    bulkActionData = { ids: ids };
    bulkActionType = "status";

    const confirmBtn = document.getElementById("confirmBulkAction");
    confirmBtn.className = "btn btn-success";
    confirmBtn.querySelector(".indicator-label").textContent = window.translations.confirm;

    const modal = new bootstrap.Modal(document.getElementById("bulkActionModal"));
    modal.show();
}

function openBulkDeleteModal() {
    const checkboxes = document.querySelectorAll(".row-checkbox:checked");
    const ids = Array.from(checkboxes).map((cb) => parseInt(cb.value));
    if (ids.length === 0) {
        Alert.warning(window.translations.bulk_select_at_least_one);
        return;
    }
    const employeeNames = getSelectedEmployeeNames(checkboxes);
    document.getElementById("bulkActionModalTitle").textContent = window.translations.delete_selected;
    document.getElementById("bulkActionMessage").textContent = window.translations.bulk_delete_confirm;
    document.getElementById("bulkStatusDropdown").style.display = "none";
    document.getElementById("bulkActionEmployeeList").innerHTML = employeeNames;
    bulkActionData = { ids: ids };
    bulkActionType = "delete";
    const confirmBtn = document.getElementById("confirmBulkAction");
    confirmBtn.className = "btn btn-danger";
    confirmBtn.querySelector(".indicator-label").textContent = window.translations.delete;
    const modal = new bootstrap.Modal(document.getElementById("bulkActionModal"));
    modal.show();
}
window.openBulkStatusModal = openBulkStatusModal;
window.openBulkDeleteModal = openBulkDeleteModal;
// ─── تأكيد الـ Bulk Action ──────────────────────────────────────────────
document.addEventListener("click", async function(e) {
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
                        payload.status =
                            document.getElementById("bulkStatusSelect").value;
                        break;

                    case "delete":
                        payload.action = "delete";
                        break;

                    case "restore": // ✅ جديد
                        payload.action = "restore";
                        break;

                    case "force_delete": // ✅ جديد
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
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                "X-Requested-With": "XMLHttpRequest",
            },
            body: JSON.stringify(payload),
        });
        const data = await response.json();
        if (data?.success) {
            Alert.success(data.message);
            if (
                window.LaravelDataTables &&
                window.LaravelDataTables["employees_datatable"]
            ) {
                window.LaravelDataTables["employees_datatable"].ajax.reload(
                    null,
                    false,
                );
            }
            // ─── التحقق من وجود محذوفين بعد العمليات ────────
            if (["delete", "restore", "force_delete"].includes(bulkActionType)) {
                setTimeout(window.checkTrashed, 500);
            }
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

document.addEventListener("hidden.bs.modal", function(e) {
    if (e.target.id === "bulkActionModal") {
        bulkActionData = null;
        bulkActionType = null;
    }
});
const Employees = (() => {
    // ─── Helpers ──────────────────────────────────────────────────────────────

    const route = (name, id) => window.routes[name].replace("__ID__", id);

    const csrfToken = () =>
        document.querySelector('meta[name="csrf-token"]').content;

    const reloadTable = () => {
        try {
            window.LaravelDataTables["employees_datatable"].ajax.reload(
                null,
                false,
            );
        } catch (e) {
            console.warn("reloadTable fallback:", e);
            $("#employees_datatable").DataTable().ajax.reload(null, false);
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

    const showConfirmModal = (
        title,
        message,
        confirmText = "تأكيد",
        callback,
    ) => {
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

    // ─── Handle Confirm Button Click ───────────────────────────────────────────

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
        el("editModalLoader").classList.toggle("d-none", !show);
        el("editEmployeeForm").classList.toggle("d-none", show);
    };

    const populateEditForm = (data) => {
        el("editEmployeeId").value = data.id;
        el("edit_name").value = data.name ?? "";
        el("edit_email").value = data.email ?? "";
        el("edit_phone").value = data.phone ?? "";
        el("edit_status").value = data.status ?? "active";
        el("edit_type").value = data.type ?? "full_time";
        el("edit_date").value = data.date ?? "";
        el("edit_department_id").value = data.department_id ?? "";
        el("edit_company_id").value = data.company_id ?? "";

        // ✅ عرض الصورة القديمة
        const editPreview = document.getElementById("editEmployeePreview");
        const editPlaceholder = document.getElementById(
            "editEmployeePlaceholder",
        );

        if (data.media && data.media.length > 0) {
            const mainMedia = data.media.find(
                (m) => m.collection_name === "employee",
            );
            if (mainMedia) {
                const baseFolder = "uploads/employee";
                const imageUrl =
                    mainMedia.disk === "direct_public"
                        ? `/${baseFolder}/${mainMedia.file_name}`
                        : `/storage/${baseFolder}/${mainMedia.file_name}`;
                editPreview.src = imageUrl;
                editPreview.style.display = "block";
                if (editPlaceholder) editPlaceholder.style.display = "none";
            } else {
                editPreview.src = "";
                editPreview.style.display = "none";
                if (editPlaceholder) editPlaceholder.style.display = "inline";
            }
        } else {
            editPreview.src = "";
            editPreview.style.display = "none";
            if (editPlaceholder) editPlaceholder.style.display = "inline";
        }
    };

    const initEdit = () => {
        // Open modal and fetch data
        document.addEventListener("click", async (e) => {
            const btn = e.target.closest(".btn-edit");
            if (!btn) return;

            getModal("editEmployeeModal").show();
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
                    hideModal("editEmployeeModal");
                    Alert.error(window.translations.error);
                }
            } catch (err) {
                console.error(err);
                hideModal("editEmployeeModal");
                Alert.error(window.translations.error);
            }
        });

        // Save edit
        document.addEventListener("click", async (e) => {
            if (!e.target.closest("#saveEditEmployee")) return;
            const saveBtn = el("saveEditEmployee");
            const id = el("editEmployeeId").value;
            const form = el("editEmployeeForm");
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
                "status",
                "type",
                "password",
                "date",
                "department_id",
                "company_id",
            ];
            fields.forEach((field) => {
                const input = form.querySelector(`[name="${field}"]`);
                if (input) {
                    formData.append(field, input.value ?? "");
                }
            });

            const imageInput = form.querySelector('[name="employee"]');
            if (imageInput && imageInput.files.length > 0) {
                formData.append("employee", imageInput.files[0]);
            }

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
                    hideModal("editEmployeeModal");
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
            if (e.target.id !== "editEmployeeModal") return;
            el("editEmployeeForm")?.reset();
            showEditLoader(true);
        });
    };

    // ─── Delete ───────────────────────────────────────────────────────────────

    const initDelete = () => {
        document.addEventListener("click", (e) => {
            const btn = e.target.closest(".btn-delete");
            if (!btn) return;
            el("deleteEmployeeId").value = btn.dataset.id;
            getModal("deleteEmployeeModal").show();
        });

        document.addEventListener("click", async (e) => {
            if (!e.target.closest("#confirmDeleteEmployee")) return;

            const confirmBtn = el("confirmDeleteEmployee");
            const id = el("deleteEmployeeId").value;
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
                    hideModal("deleteEmployeeModal");
                    reloadTable();
                    setTimeout(checkTrashed, 500);
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
            if (e.target.id !== "deleteEmployeeModal") return;
            el("deleteEmployeeId").value = "";
        });
    };

    // ─── Image Preview ──────────────────────────────────────────────────────

    const initImagePreview = () => {
        // Create Modal Preview
        const createInput = document.getElementById("employeeInput");
        const createPreview = document.getElementById("employeePreview");
        const createPlaceholder = document.getElementById(
            "employeePlaceholder",
        );

        if (createInput) {
            createInput.addEventListener("change", function (e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function (event) {
                        createPreview.src = event.target.result;
                        createPreview.style.display = "block";
                        if (createPlaceholder)
                            createPlaceholder.style.display = "none";
                    };
                    reader.readAsDataURL(file);
                } else {
                    createPreview.src = "";
                    createPreview.style.display = "none";
                    if (createPlaceholder)
                        createPlaceholder.style.display = "inline";
                }
            });
        }

        // Edit Modal Preview
        document.addEventListener("change", function (e) {
            const editInput = e.target.closest("#editEmployeeInput");
            if (!editInput) return;

            const editPreview = document.getElementById("editEmployeePreview");
            const editPlaceholder = document.getElementById(
                "editEmployeePlaceholder",
            );

            const file = editInput.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (event) {
                    editPreview.src = event.target.result;
                    editPreview.style.display = "block";
                    if (editPlaceholder) editPlaceholder.style.display = "none";
                };
                reader.readAsDataURL(file);
            } else {
                editPreview.src = "";
                editPreview.style.display = "none";
                if (editPlaceholder) editPlaceholder.style.display = "inline";
            }
        });

        // Reset on modal close
        const createModal = document.getElementById("createEmployeeModal");
        if (createModal) {
            createModal.addEventListener("hidden.bs.modal", function () {
                const input = document.getElementById("employeeInput");
                const preview = document.getElementById("employeePreview");
                const placeholder = document.getElementById(
                    "employeePlaceholder",
                );
                if (input) input.value = "";
                if (preview) {
                    preview.src = "";
                    preview.style.display = "none";
                }
                if (placeholder) placeholder.style.display = "inline";
            });
        }

        const editModal = document.getElementById("editEmployeeModal");
        if (editModal) {
            editModal.addEventListener("hidden.bs.modal", function () {
                const input = document.getElementById("editEmployeeInput");
                const preview = document.getElementById("editEmployeePreview");
                const placeholder = document.getElementById(
                    "editEmployeePlaceholder",
                );
                if (input) input.value = "";
                if (preview) {
                    preview.src = "";
                    preview.style.display = "none";
                }
                if (placeholder) placeholder.style.display = "inline";
            });
        }
    };

    // ─── Open Image Modal ──────────────────────────────────────────────────

    function openImageModal(src, title) {
        if (!src || src === "") {
            if (typeof Alert !== "undefined") {
                Alert.warning("لا توجد صورة لعرضها");
            }
            return;
        }

        const modalHtml = `
            <div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">${title || "الصوره"}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center">
                            <img src="${src}" alt="الصوره" style="max-width: 100%; max-height: 70vh; border-radius: 8px;">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        const existingModal = document.getElementById("imagePreviewModal");
        if (existingModal) {
            existingModal.remove();
        }

        document.body.insertAdjacentHTML("beforeend", modalHtml);

        const modal = new bootstrap.Modal(
            document.getElementById("imagePreviewModal"),
        );
        modal.show();

        document
            .getElementById("imagePreviewModal")
            .addEventListener("hidden.bs.modal", function () {
                this.remove();
            });
    }

    // ─── Check Trashed ──────────────────────────────────────────────────────────
    const checkTrashed = () => {
        fetch(window.routes.hasTrashed)
            .then((response) => response.json())
            .then((data) => {
                const toggleBtn = document.getElementById("toggleTrashed");
                if (data.hasTrashed) {
                    toggleBtn.style.display = "inline-flex";
                } else {
                    toggleBtn.style.display = "none";
                    if (window.showTrashed) {
                        window.showTrashed = false;
                        const btnText = document.getElementById("trashedBtnText");
                        btnText.textContent = window.translations.show_trashed;
                        const table = window.LaravelDataTables["employees_datatable"];
                        table.ajax.url(window.routes.index + "?show_trashed=false").load();
                        setTimeout(function () {
                            if (typeof attachBulkButtonEvents === "function") {
                                attachBulkButtonEvents();
                            }
                        }, 500);
                    }
                }
                setTimeout(function () {
                    if (typeof attachBulkButtonEvents === "function") {
                        attachBulkButtonEvents();
                    }
                }, 300);
            })
            .catch(() => {
                document.getElementById("toggleTrashed").style.display = "none";
            });
    };
    window.checkTrashed = checkTrashed;
    // ─── Restore ───────────────────────────────────────────────────────────────

    const initRestore = () => {
        document.addEventListener("click", async (e) => {
            const btn = e.target.closest(".btn-restore");
            if (!btn) return;
            const route = btn.dataset.route;
            showConfirmModal(
                window.translations.restore || "استعادة",
                window.translations.restore_confirm ||
                    "هل أنت متأكد من استعادة هذا الموظف؟",
                window.translations.restore || "استعادة",
                async (confirmBtn) => {
                    try {
                        const res = await fetchJson(route, "PATCH");
                        const data = await parseJson(res);
                        if (data?.success) {
                            Alert.success(data.message);
                            reloadTable();
                            setTimeout(checkTrashed, 500);
                        } else {
                            Alert.error(
                                data?.message || window.translations.error,
                            );
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
            const route = btn.dataset.route;
            showConfirmModal(
                window.translations.force_delete || "حذف نهائي",
                window.translations.force_delete_confirm ||
                    "تحذير! هذا الإجراء لا يمكن التراجع عنه. هل أنت متأكد؟",
                window.translations.force_delete || "حذف نهائي",
                async (confirmBtn) => {
                    try {
                        const res = await fetchJson(route, "DELETE");
                        const data = await parseJson(res);
                        if (data?.success) {
                            Alert.success(data.message);
                            reloadTable();
                            setTimeout(checkTrashed, 500);
                        } else {
                            Alert.error(
                                data?.message || window.translations.error,
                            );
                        }
                    } catch (err) {
                        console.error(err);
                        Alert.error(window.translations.error);
                    }
                },
            );
        });
    };

    // ─── Bulk Restore Modal ────────────────────────────────────────────────

    function openBulkRestoreModal() {
        const checkboxes = document.querySelectorAll(".row-checkbox:checked");
        const ids = Array.from(checkboxes).map((cb) => parseInt(cb.value));

        if (ids.length === 0) {
            Alert.warning(window.translations.bulk_select_at_least_one);
            return;
        }

        const employeeNames = getSelectedEmployeeNames(checkboxes);

        document.getElementById("bulkActionModalTitle").textContent =
            window.translations.bulk_restore || "استعادة جماعية";
        document.getElementById("bulkActionMessage").textContent =
            window.translations.bulk_restore_confirm ||
            "هل أنت متأكد من استعادة الموظفين المحددين؟";
        document.getElementById("bulkStatusDropdown").style.display = "none";
        document.getElementById("bulkActionEmployeeList").innerHTML =
            employeeNames;

        bulkActionData = { ids: ids };
        bulkActionType = "restore"; // ✅ نوع جديد

        const confirmBtn = document.getElementById("confirmBulkAction");
        confirmBtn.className = "btn btn-success";
        confirmBtn.querySelector(".indicator-label").textContent =
            window.translations.restore || "استعادة";

        const modal = new bootstrap.Modal(
            document.getElementById("bulkActionModal"),
        );
        modal.show();
    }
    window.openBulkRestoreModal = openBulkRestoreModal;

    // ─── Bulk Force Delete Modal ────────────────────────────────────────────

    function openBulkForceDeleteModal() {
        const checkboxes = document.querySelectorAll(".row-checkbox:checked");
        const ids = Array.from(checkboxes).map((cb) => parseInt(cb.value));

        if (ids.length === 0) {
            Alert.warning(window.translations.bulk_select_at_least_one);
            return;
        }

        const employeeNames = getSelectedEmployeeNames(checkboxes);

        document.getElementById("bulkActionModalTitle").textContent = window.translations.bulk_force_delete || "حذف نهائي جماعي";
        document.getElementById("bulkActionMessage").textContent = window.translations.bulk_force_delete_confirm ||
            "تحذير! هذا الإجراء لا يمكن التراجع عنه. هل أنت متأكد؟";
        document.getElementById("bulkStatusDropdown").style.display = "none";
        document.getElementById("bulkActionEmployeeList").innerHTML =
            employeeNames;

        bulkActionData = { ids: ids };
        bulkActionType = "force_delete"; // ✅ نوع جديد

        const confirmBtn = document.getElementById("confirmBulkAction");
        confirmBtn.className = "btn btn-danger";
        confirmBtn.querySelector(".indicator-label").textContent =
            window.translations.force_delete || "حذف نهائي";

        const modal = new bootstrap.Modal(
            document.getElementById("bulkActionModal"),
        );
        modal.show();
    }
    window.openBulkForceDeleteModal = openBulkForceDeleteModal;

    // ─── Init ─────────────────────────────────────────────────────────────────

    const init = () => {
        initToggleStatus();
        initEdit();
        initDelete();
        initImagePreview();
        initRestore();
        initForceDelete();
        setTimeout(() => {
            checkTrashed();
        }, 500);
    };

    return { init };
})();

window.openImageModal = function (src, title) {
    if (!src || src === "") {
        if (typeof Alert !== "undefined") {
            Alert.warning("لا توجد صورة لعرضها");
        }
        return;
    }

    const modalHtml = `
        <div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">${title || "الصوره"}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center">
                        <img src="${src}" alt="الصوره" style="max-width: 100%; max-height: 70vh; border-radius: 8px;">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                    </div>
                </div>
            </div>
        </div>
    `;

    const existingModal = document.getElementById("imagePreviewModal");
    if (existingModal) {
        existingModal.remove();
    }

    document.body.insertAdjacentHTML("beforeend", modalHtml);

    const modal = new bootstrap.Modal(
        document.getElementById("imagePreviewModal"),
    );
    modal.show();

    document
        .getElementById("imagePreviewModal")
        .addEventListener("hidden.bs.modal", function () {
            this.remove();
        });
};

document.addEventListener("DOMContentLoaded", Employees.init);
