"use strict";
/**
 * ============================================================
 * MAIN FUNCTIONS: Employee Management CRUD Operations
 * ============================================================
 * Toggle Status, Edit, Delete, Force Delete, Restore Employee
 * Image Preview
 **/
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

    const editPreview = document.getElementById("editEmployeePreview");
    const editPlaceholder = document.getElementById("editEmployeePlaceholder");

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
    document.addEventListener("click", async (e) => {
        const btn = e.target.closest(".btn-edit");
        if (!btn) return;

        getModal("editEmployeeModal").show();
        showEditLoader(true);

        try {
            const res = await fetchJson(route("edit", btn.dataset.id), "GET");
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
        if (e.target.id !== "deleteEmployeeModal") return;
        el("deleteEmployeeId").value = "";
    });
};

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

// ─── Image Preview ──────────────────────────────────────────────────────
const initImagePreview = () => {
    const createInput = document.getElementById("employeeInput");
    const createPreview = document.getElementById("employeePreview");
    const createPlaceholder = document.getElementById("employeePlaceholder");

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

    const createModal = document.getElementById("createEmployeeModal");
    if (createModal) {
        createModal.addEventListener("hidden.bs.modal", function () {
            const input = document.getElementById("employeeInput");
            const preview = document.getElementById("employeePreview");
            const placeholder = document.getElementById("employeePlaceholder");
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

// ─── Init ─────────────────────────────────────────────────────────────────
const init = () => {
    initToggleStatus();
    initEdit();
    initDelete();
    initImagePreview();
    initRestore();
    initForceDelete();
};

// ─── تعريف Employees كـ object عالمي ──────────────────────────────────────
window.Employees = {
    init: init,
    reloadTable: reloadTable,
    fetchJson: fetchJson,
    parseJson: parseJson,
    setBtnLoading: setBtnLoading,
    showConfirmModal: showConfirmModal,
    getModal: getModal,
    hideModal: hideModal,
};
