"use strict";

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

            const payload = {
                name: form.querySelector('[name="name"]')?.value,
                email: form.querySelector('[name="email"]')?.value,
                phone: form.querySelector('[name="phone"]')?.value,
                status: form.querySelector('[name="status"]')?.value,
                type: form.querySelector('[name="type"]')?.value,
                password: form.querySelector('[name="password"]')?.value,
                date: form.querySelector('[name="date"]')?.value,
                department_id: form.querySelector('[name="department_id"]')
                    ?.value,
                company_id: form.querySelector('[name="company_id"]')?.value,
            };

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

    // ─── Init ─────────────────────────────────────────────────────────────────

    const init = () => {
        initToggleStatus();
        initEdit();
        initDelete();
    };

    return { init };
})();

document.addEventListener("DOMContentLoaded", Employees.init);
