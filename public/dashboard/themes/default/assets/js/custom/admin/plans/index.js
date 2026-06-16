"use strict";

const Plans = (() => {
    // ─── Helpers ──────────────────────────────────────────────────────────────

    const route = (name, id) => window.routes[name].replace("__ID__", id);

    const csrfToken = () =>
        document.querySelector('meta[name="csrf-token"]').content;

    const reloadTable = () => {
        try {
            window.LaravelDataTables["plans_datatable"].ajax.reload(
                null,
                false,
            );
        } catch (e) {
            console.warn("reloadTable fallback:", e);
            $("#plans_datatable").DataTable().ajax.reload(null, false);
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

        if (method === "POST") {
            return fetch(url, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    Accept: "application/json",
                    "X-CSRF-TOKEN": csrfToken(),
                    "X-Requested-With": "XMLHttpRequest",
                },
                body: JSON.stringify(body),
            });
        }

        const formData = new FormData();
        formData.append("_method", method);
        formData.append("_token", csrfToken());

        const flattenToFormData = (data, prefix = "") => {
            Object.entries(data).forEach(([key, val]) => {
                const k = prefix ? `${prefix}[${key}]` : key;
                if (
                    val !== null &&
                    typeof val === "object" &&
                    !Array.isArray(val)
                ) {
                    flattenToFormData(val, k);
                } else {
                    formData.append(k, val ?? "");
                }
            });
        };
        flattenToFormData(body);

        return fetch(url, {
            method: "POST",
            headers: {
                Accept: "application/json",
                "X-CSRF-TOKEN": csrfToken(),
                "X-Requested-With": "XMLHttpRequest",
            },
            body: formData,
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
        document.addEventListener("change", async (e) => {
            const toggle = e.target.closest(".toggle-status");
            if (!toggle) return;

            const row = toggle.closest("tr");

            try {
                const res = await fetchJson(toggle.dataset.route, "PATCH");
                const data = await parseJson(res);

                if (!data) {
                    toggle.checked = !toggle.checked;
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
                    toggle.checked = !toggle.checked;
                }
            } catch {
                Alert.error(window.translations.error);
                toggle.checked = !toggle.checked;
            }
        });
    };

    // ─── Edit ─────────────────────────────────────────────────────────────────

    const showEditLoader = (show) => {
        el("editModalLoader").classList.toggle("d-none", !show);
        el("editPlanForm").classList.toggle("d-none", show);
    };

    const populateEditForm = (data) => {
        el("editPlanId").value = data.id;

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

        el("edit_price").value = data.price ?? "";
        el("edit_billing_cycle").value = data.billing_cycle ?? "monthly";
        el("edit_company_id").value = data.company_id ?? "";
        el("edit_status").checked = !!+data.status;
    };

    const initEdit = () => {
        // Open modal and fetch data
        document.addEventListener("click", async (e) => {
            const btn = e.target.closest(".btn-edit");
            if (!btn) return;

            getModal("editPlanModal").show();
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
                    hideModal("editPlanModal");
                    Alert.error(window.translations.error);
                }
            } catch (err) {
                console.error(err);
                hideModal("editPlanModal");
                Alert.error(window.translations.error);
            }
        });

        // Save edit
        document.addEventListener("click", async (e) => {
            if (!e.target.closest("#saveEditPlan")) return;

            const saveBtn = el("saveEditPlan");
            const id = el("editPlanId").value;
            const form = el("editPlanForm");

            if (!id) {
                Alert.error(window.translations.error);
                return;
            }

            const payload = {
                name: {},
                description: {},
            };

            (window.locales || []).forEach((locale) => {
                const nameInput = form.querySelector(
                    `[name="name[${locale}]"]`,
                );
                if (nameInput) payload.name[locale] = nameInput.value;

                const descInput = form.querySelector(
                    `[name="description[${locale}]"]`,
                );
                if (descInput) payload.description[locale] = descInput.value;
            });

            payload.price = form.querySelector('[name="price"]')?.value || 0;
            payload.billing_cycle =
                form.querySelector('[name="billing_cycle"]')?.value ||
                "monthly";
            payload.company_id =
                form.querySelector('[name="company_id"]')?.value || null;
            payload.status = form.querySelector('[name="status"]')?.checked
                ? 1
                : 0;

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
                    hideModal("editPlanModal");
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
            if (e.target.id !== "editPlanModal") return;
            el("editPlanForm")?.reset();
            showEditLoader(true);
            const firstTab = document.querySelector("#editLangTabs .nav-link");
            if (firstTab) bootstrap.Tab.getOrCreateInstance(firstTab).show();
        });
    };

    // ─── Delete ───────────────────────────────────────────────────────────────

    const initDelete = () => {
        document.addEventListener("click", (e) => {
            const btn = e.target.closest(".btn-delete");
            if (!btn) return;
            el("deletePlanId").value = btn.dataset.id;
            getModal("deletePlanModal").show();
        });

        document.addEventListener("click", async (e) => {
            if (!e.target.closest("#confirmDeletePlan")) return;

            const confirmBtn = el("confirmDeletePlan");
            const id = el("deletePlanId").value;
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
                    hideModal("deletePlanModal");
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
            if (e.target.id !== "deletePlanModal") return;
            el("deletePlanId").value = "";
        });
    };

    // ─── Toggle Billing Cycle ──────────────────────────────────────────────────

    // ─── Toggle Billing Cycle ──────────────────────────────────────────────────

    // ─── Toggle Billing Cycle (Dropdown Version - Simplified) ──────────────────

    const initToggleBillingCycle = () => {
        document.addEventListener("click", async (e) => {
            const item = e.target.closest(".toggle-billing-cycle-item");
            if (!item) return;

            e.preventDefault();

            const dropdown = item.closest(".dropdown");
            const button = dropdown?.querySelector(".toggle-billing-cycle-btn");
            const route = item.dataset.route;
            const newValue = item.dataset.value;

            // Save current button text
            const originalText = button?.innerHTML || "";

            // Show loading
            if (button) {
                button.disabled = true;
                button.innerHTML = `
                <span class="spinner-border spinner-border-sm me-1" role="status"></span>
            `;
            }

            try {
                const res = await fetchJson(route, "PATCH");
                const data = await parseJson(res);

                if (!data) {
                    Alert.error(window.translations.error);
                    if (button) {
                        button.disabled = false;
                        button.innerHTML = originalText;
                    }
                    return;
                }

                if (data.success) {
                    Alert.success(data.message);

                    // Update button
                    if (button) {
                        const isMonthly = newValue === "monthly";
                        const label = isMonthly ? "شهري" : "سنوي";
                        const badgeClass = isMonthly
                            ? "bg-success"
                            : "bg-primary";

                        button.textContent = label;
                        button.className = `btn btn-sm ${badgeClass} dropdown-toggle toggle-billing-cycle-btn`;
                        button.disabled = false;
                    }

                    // Update active state
                    const allItems = dropdown?.querySelectorAll(
                        ".toggle-billing-cycle-item",
                    );
                    allItems?.forEach((el) => {
                        if (el.dataset.value === newValue) {
                            el.classList.add("active");
                        } else {
                            el.classList.remove("active");
                        }
                    });

                    // Update the badge in the row (if exists)
                    const row = item.closest("tr");
                    const badgeEl = row?.querySelector(".badge-cycle");
                    if (badgeEl) {
                        const isMonthly = newValue === "monthly";
                        const badgeLabel = isMonthly ? "شهري" : "سنوي";
                        const badgeClass = isMonthly ? "bg-success" : "bg-info";
                        badgeEl.className = `badge ${badgeClass} badge-cycle`;
                        badgeEl.textContent = badgeLabel;
                    }

                    // Close dropdown
                    const dropdownMenu =
                        dropdown?.querySelector(".dropdown-menu");
                    if (dropdownMenu) {
                        const bsDropdown =
                            bootstrap.Dropdown.getInstance(button);
                        if (bsDropdown) {
                            bsDropdown.hide();
                        }
                    }
                } else {
                    Alert.error(data.message || window.translations.error);
                    if (button) {
                        button.disabled = false;
                        button.innerHTML = originalText;
                    }
                }
            } catch (err) {
                console.error("Toggle Billing Cycle Error:", err);
                Alert.error(window.translations.error);
                if (button) {
                    button.disabled = false;
                    button.innerHTML = originalText;
                }
            }
        });
    };

    // ─── Features Management ────────────────────────────────────────────────────

    const initFeaturesManagement = () => {
        // Open features modal
        document.addEventListener("click", async (e) => {
            const btn = e.target.closest(".btn-features");
            if (!btn) return;

            const planId = btn.dataset.id;
            const modal = getModal("featuresPlanModal");
            modal.show();

            // Show loader
            document
                .getElementById("featuresModalLoader")
                .classList.remove("d-none");
            document.getElementById("featuresContent").classList.add("d-none");

            try {
                const res = await fetchJson(
                    route("getFeatures", planId),
                    "GET",
                );
                const data = await parseJson(res);

                if (data?.success) {
                    populateFeaturesForm(data.data);
                    document
                        .getElementById("featuresModalLoader")
                        .classList.add("d-none");
                    document
                        .getElementById("featuresContent")
                        .classList.remove("d-none");
                } else {
                    Alert.error(window.translations.error);
                    modal.hide();
                }
            } catch (err) {
                console.error("Error loading features:", err);
                Alert.error(window.translations.error);
                modal.hide();
            }
        });

        // Populate features form
        const populateFeaturesForm = (data) => {
            document.getElementById("featuresPlanId").value = data.plan_id;
            const tbody = document.getElementById("featuresTableBody");
            tbody.innerHTML = "";

            if (data.features.length === 0) {
                tbody.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">
                        <i class="ti ti-list-off fs-3 d-block mb-2"></i>
                        ${window.translations.no_features || "لا توجد مميزات متاحة"}
                    </td>
                </tr>
            `;
                return;
            }

            data.features.forEach((feature) => {
                const tr = document.createElement("tr");
                tr.className = feature.is_included ? "included" : "";
                tr.dataset.featureId = feature.id;

                tr.innerHTML = `
                <td class="text-center">
                    <input type="checkbox" 
                           class="form-check-input feature-checkbox" 
                           name="features[${feature.id}][is_included]"
                           ${feature.is_included ? "checked" : ""}>
                </td>
                <td>
                    <strong>${feature.name}</strong>
                </td>
                <td>
                    ${feature.type.badge}
                </td>
                <td>
                    ${feature.scope.badge}
                </td>
                <td>
                    <input type="number" 
                           class="form-control form-control-sm limit-input" 
                           name="features[${feature.id}][limit]"
                           value="${feature.limit || ""}"
                           placeholder="∞"
                           min="0"
                           ${!feature.is_included ? "disabled" : ""}>
                </td>
            `;

                tbody.appendChild(tr);
            });

            // Handle checkbox change to enable/disable limit input
            tbody.querySelectorAll(".feature-checkbox").forEach((checkbox) => {
                checkbox.addEventListener("change", function () {
                    const row = this.closest("tr");
                    const limitInput = row.querySelector(".limit-input");
                    limitInput.disabled = !this.checked;
                    row.classList.toggle("included", this.checked);
                    row.style.opacity = this.checked ? "1" : "0.6";
                });
            });

            // Select All
            const selectAll = document.getElementById("selectAllFeatures");
            selectAll.checked = false;
            // Remove old event listener if exists
            const newSelectAll = selectAll.cloneNode(true);
            selectAll.parentNode.replaceChild(newSelectAll, selectAll);

            newSelectAll.addEventListener("change", function () {
                const checkboxes = tbody.querySelectorAll(".feature-checkbox");
                checkboxes.forEach((cb) => {
                    cb.checked = this.checked;
                    cb.dispatchEvent(new Event("change"));
                });
            });
        };

        // Save features
        document.addEventListener("click", async (e) => {
            if (!e.target.closest("#savePlanFeatures")) return;

            const saveBtn = document.getElementById("savePlanFeatures");
            const planId = document.getElementById("featuresPlanId").value;
            const form = document.getElementById("featuresForm");
            const formData = new FormData(form);

            setBtnLoading(saveBtn, true);

            try {
                const payload = {};
                formData.forEach((value, key) => {
                    const match = key.match(/features\[(\d+)\]\[(.+)\]/);
                    if (match) {
                        const featureId = match[1];
                        const field = match[2];
                        if (!payload[featureId]) payload[featureId] = {};

                        if (field === "is_included") {
                            payload[featureId][field] = value === "on";
                        } else if (field === "limit") {
                            payload[featureId][field] = value
                                ? parseInt(value)
                                : null;
                        }
                    }
                });

                const features = {};
                Object.keys(payload).forEach((id) => {
                    features[id] = {
                        is_included: payload[id].is_included || false,
                        limit: payload[id].limit || null,
                    };
                });

                const res = await fetchJson(
                    route("updateFeatures", planId),
                    "POST",
                    { features: features },
                );
                const data = await parseJson(res);

                if (data?.success) {
                    Alert.success(data.message);
                    hideModal("featuresPlanModal");
                    reloadTable();
                } else {
                    Alert.error(data?.message || window.translations.error);
                }
            } catch (err) {
                console.error("Error saving features:", err);
                Alert.error(window.translations.error);
            } finally {
                setBtnLoading(saveBtn, false);
            }
        });

        // Reset on close
        document.addEventListener("hidden.bs.modal", (e) => {
            if (e.target.id !== "featuresPlanModal") return;
            document
                .getElementById("featuresModalLoader")
                .classList.remove("d-none");
            document.getElementById("featuresContent").classList.add("d-none");
            document.getElementById("featuresForm")?.reset();
        });
    };

    // ─── Init ─────────────────────────────────────────────────────────────────

    const init = () => {
        initToggleStatus();
        initToggleBillingCycle();
        initEdit();
        initDelete();
        initFeaturesManagement();
    };

    return { init };
})();

document.addEventListener("DOMContentLoaded", Plans.init);