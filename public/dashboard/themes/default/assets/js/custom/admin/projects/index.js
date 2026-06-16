"use strict";

const Projects = (() => {
    // ─── Helpers ──────────────────────────────────────────────────────────────

    const route = (name, id) => window.routes[name].replace("__ID__", id);

    const csrfToken = () =>
        document.querySelector('meta[name="csrf-token"]').content;

    const reloadTable = () => {
        try {
            window.LaravelDataTables["projects_datatable"].ajax.reload(
                null,
                false,
            );
        } catch (e) {
            console.warn("reloadTable fallback:", e);
            $("#projects_datatable").DataTable().ajax.reload(null, false);
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

    // ─── Image Preview ──────────────────────────────────────────────────────

    const initImagePreview = () => {
        // Create Modal Preview
        const createInput = document.getElementById("projectInput");
        const createPreview = document.getElementById("projectPreview");
        const createPlaceholder = document.getElementById("imagePlaceholder");

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

        // ✅ Edit Modal Preview - باستخدام Event Delegation
        document.addEventListener("change", function (e) {
            const editInput = e.target.closest("#editProjectInput");
            if (!editInput) return;

            const editPreview = document.getElementById("editProjectPreview");
            const editPlaceholder = document.getElementById(
                "editImagePlaceholder",
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
        const createModal = document.getElementById("createProjectModal");
        if (createModal) {
            createModal.addEventListener("hidden.bs.modal", function () {
                const input = document.getElementById("projectInput");
                const preview = document.getElementById("projectPreview");
                const placeholder = document.getElementById("imagePlaceholder");
                if (input) input.value = "";
                if (preview) {
                    preview.src = "";
                    preview.style.display = "none";
                }
                if (placeholder) placeholder.style.display = "inline";
            });
        }

        const editModal = document.getElementById("editProjectModal");
        if (editModal) {
            editModal.addEventListener("hidden.bs.modal", function () {
                const input = document.getElementById("editProjectInput");
                const preview = document.getElementById("editProjectPreview");
                const placeholder = document.getElementById(
                    "editImagePlaceholder",
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
        el("editProjectForm").classList.toggle("d-none", show);
    };

    const populateEditForm = (data) => {
        el("editProjectId").value = data.id;

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

        el("edit_status").value = data.status || "draft";
        el("edit_company_id").value = data.company_id ?? "";

        // Project Types
        const projectTypeIds = data.project_types?.map((pt) => pt.id) || [];
        document
            .querySelectorAll('#editProjectForm input[name="project_types[]"]')
            .forEach((checkbox) => {
                checkbox.checked = projectTypeIds.includes(
                    parseInt(checkbox.value),
                );
            });

        // Modules
        const moduleIds = data.modules?.map((m) => m.id) || [];
        document
            .querySelectorAll('#editProjectForm input[name="modules[]"]')
            .forEach((checkbox) => {
                checkbox.checked = moduleIds.includes(parseInt(checkbox.value));
            });

        // ✅ Image Preview - عرض الصورة القديمة
        const editPreview = document.getElementById("editProjectPreview");
        const editPlaceholder = document.getElementById("editImagePlaceholder");
        if (data.media && data.media.length > 0) {
            const mainMedia = data.media.find(
                (m) => m.collection_name === "project",
            );
            if (mainMedia) {
                const baseFolder = "uploads/project";
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

            getModal("editProjectModal").show();
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
                    hideModal("editProjectModal");
                    Alert.error(window.translations.error);
                }
            } catch (err) {
                console.error(err);
                hideModal("editProjectModal");
                Alert.error(window.translations.error);
            }
        });

        // Save edit
        document.addEventListener("click", async (e) => {
            if (!e.target.closest("#saveEditProject")) return;

            const saveBtn = el("saveEditProject");
            const id = el("editProjectId").value;
            const form = el("editProjectForm");

            if (!id) {
                Alert.error(window.translations.error);
                return;
            }

            const formData = new FormData();
            formData.append("_method", "PUT");
            formData.append("_token", csrfToken());

            (window.locales || []).forEach((locale) => {
                const nameInput = form.querySelector(
                    `[name="name[${locale}]"]`,
                );
                if (nameInput)
                    formData.append(`name[${locale}]`, nameInput.value);

                const descInput = form.querySelector(
                    `[name="description[${locale}]"]`,
                );
                if (descInput)
                    formData.append(`description[${locale}]`, descInput.value);
            });

            const status =
                form.querySelector('[name="status"]')?.value || "draft";
            formData.append("status", status);

            const companyId =
                form.querySelector('[name="company_id"]')?.value || null;
            if (companyId) formData.append("company_id", companyId);

            const projectTypes = [];
            form.querySelectorAll(
                'input[name="project_types[]"]:checked',
            ).forEach((checkbox) => {
                projectTypes.push(parseInt(checkbox.value));
            });
            formData.append("project_types", projectTypes.join(","));

            const modules = [];
            form.querySelectorAll('input[name="modules[]"]:checked').forEach(
                (checkbox) => {
                    modules.push(parseInt(checkbox.value));
                },
            );
            formData.append("modules", modules.join(","));

            const imageInput = form.querySelector('[name="project"]');
            if (imageInput && imageInput.files.length > 0) {
                formData.append("project", imageInput.files[0]);
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
                    hideModal("editProjectModal");
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
            if (e.target.id !== "editProjectModal") return;
            el("editProjectForm")?.reset();
            showEditLoader(true);
            const firstTab = document.querySelector("#editLangTabs .nav-link");
            if (firstTab) bootstrap.Tab.getOrCreateInstance(firstTab).show();

            const preview = document.getElementById("editProjectPreview");
            const placeholder = document.getElementById("editImagePlaceholder");
            if (preview) {
                preview.src = "";
                preview.style.display = "none";
            }
            if (placeholder) placeholder.style.display = "inline";
        });
    };

    // ─── Delete ───────────────────────────────────────────────────────────────

    const initDelete = () => {
        document.addEventListener("click", (e) => {
            const btn = e.target.closest(".btn-delete");
            if (!btn) return;
            el("deleteProjectId").value = btn.dataset.id;
            getModal("deleteProjectModal").show();
        });

        document.addEventListener("click", async (e) => {
            if (!e.target.closest("#confirmDeleteProject")) return;

            const confirmBtn = el("confirmDeleteProject");
            const id = el("deleteProjectId").value;
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
                    hideModal("deleteProjectModal");
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
            if (e.target.id !== "deleteProjectModal") return;
            el("deleteProjectId").value = "";
        });
    };

    // ─── Init ─────────────────────────────────────────────────────────────────

    const init = () => {
        initToggleStatus();
        initEdit();
        initDelete();
        initImagePreview();
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

document.addEventListener("DOMContentLoaded", Projects.init);