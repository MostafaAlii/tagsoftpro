/**
 * ============================================================
 * 📁 FILE: trashed_manager.js
 * 📌 MAIN FUNCTIONS: Trashed Menu Items Management
 * ============================================================
 */

"use strict";

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
                    const table =
                        window.LaravelDataTables["menu_items_datatable"];
                    table.ajax
                        .url(window.routes.index + "?show_trashed=false")
                        .load();
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

// ─── دالة بناء الـ HTML للـ Bulk Actions ──────────────────────
function buildBulkActionsHTML() {
    let html = `
        <button class="dt-button buttons-collection btn btn-warning" tabindex="0" aria-controls="menu_items_datatable" type="button" id="bulkActionsBtn">
            <span>
                <i class="ti ti-settings me-1"></i>
                ${window.translations.bulk_actions || "إجراءات جماعية"}
                <span class="badge bg-light text-dark ms-1" id="selectedCount">0</span>
            </span>
        </button>
        <div class="dt-button-collection" style="display: none; position: absolute; top: 100%; right: 0; z-index: 99999; min-width: 200px; margin-top: 5px; background: #fff; border-radius: 10px; box-shadow: 0 8px 24px rgba(0,0,0,0.15); border: 1px solid #e0e0e0; padding: 8px 0;">
    `;

    if (window.showTrashed) {
        html += `
            <a class="dropdown-item text-success" href="#" data-action="restore" style="padding: 10px 24px; display: block; text-decoration: none; color: #28a745; transition: all 0.2s ease; cursor: pointer; font-size: 14px;">
                <i class="ti ti-refresh me-2 text-success"></i> ${window.translations.bulk_restore || "استعادة جماعية"}
            </a>
            <a class="dropdown-item text-danger" href="#" data-action="force_delete" style="padding: 10px 24px; display: block; text-decoration: none; color: #dc3545; transition: all 0.2s ease; cursor: pointer; font-size: 14px;">
                <i class="ti ti-trash-off me-2 text-danger"></i> ${window.translations.bulk_force_delete || "حذف نهائي جماعي"}
            </a>
        `;
    } else {
        html += `
            <a class="dropdown-item" href="#" data-action="status" style="padding: 10px 24px; display: block; text-decoration: none; color: #333; transition: all 0.2s ease; cursor: pointer; font-size: 14px;">
                <i class="ti ti-exchange me-2 text-primary"></i> ${window.translations.bulk_change_status || "تغيير الحالة"}
            </a>
            <a class="dropdown-item text-danger" href="#" data-action="delete" style="padding: 10px 24px; display: block; text-decoration: none; color: #dc3545; transition: all 0.2s ease; cursor: pointer; font-size: 14px;">
                <i class="ti ti-trash me-2 text-danger"></i> ${window.translations.delete_selected || "حذف المحدد"}
            </a>
        `;
    }

    html += `</div>`;
    return html;
}

// ─── دالة ربط الأحداث بالأزرار ──────────────────────────────
function attachBulkButtonEvents() {
    const bulkBtn = document.getElementById("bulkActionsBtn");
    if (!bulkBtn) return;

    const newBtn = bulkBtn.cloneNode(true);
    bulkBtn.parentNode.replaceChild(newBtn, bulkBtn);

    let oldCollection = newBtn.nextElementSibling;
    if (
        oldCollection &&
        oldCollection.classList.contains("dt-button-collection")
    ) {
        oldCollection.remove();
    }

    let dropdownHTML = `
    <div class="dt-button-collection" style="display: none; position: absolute; top: 100%; right: 0; z-index: 99999; min-width: 200px; margin-top: 5px; background: #fff; border-radius: 10px; box-shadow: 0 8px 24px rgba(0,0,0,0.15); border: 1px solid #e0e0e0; padding: 8px 0;">
    `;

    if (window.showTrashed) {
        dropdownHTML += `
        <a class="dropdown-item text-success" href="#" data-action="restore" style="padding: 10px 24px; display: block; text-decoration: none; color: #28a745; transition: all 0.2s ease; cursor: pointer; font-size: 14px;">
            <i class="ti ti-refresh me-2 text-success"></i> ${window.translations.bulk_restore || "استعادة جماعية"}
        </a>
        <a class="dropdown-item text-danger" href="#" data-action="force_delete" style="padding: 10px 24px; display: block; text-decoration: none; color: #dc3545; transition: all 0.2s ease; cursor: pointer; font-size: 14px;">
            <i class="ti ti-trash-off me-2 text-danger"></i> ${window.translations.bulk_force_delete || "حذف نهائي جماعي"}
        </a>
        `;
    } else {
        dropdownHTML += `
        <a class="dropdown-item" href="#" data-action="status" style="padding: 10px 24px; display: block; text-decoration: none; color: #333; transition: all 0.2s ease; cursor: pointer; font-size: 14px;">
            <i class="ti ti-exchange me-2 text-primary"></i> ${window.translations.bulk_change_status || "تغيير الحالة"}
        </a>
        <a class="dropdown-item text-danger" href="#" data-action="delete" style="padding: 10px 24px; display: block; text-decoration: none; color: #dc3545; transition: all 0.2s ease; cursor: pointer; font-size: 14px;">
            <i class="ti ti-trash me-2 text-danger"></i> ${window.translations.delete_selected || "حذف المحدد"}
        </a>
        `;
    }

    dropdownHTML += `</div>`;
    newBtn.insertAdjacentHTML("afterend", dropdownHTML);

    const finalBtn = document.getElementById("bulkActionsBtn");
    const collection = finalBtn?.nextElementSibling;

    if (finalBtn) {
        finalBtn.addEventListener("click", function (e) {
            e.stopPropagation();
            e.preventDefault();
            if (collection) {
                const isHidden =
                    collection.style.display === "none" ||
                    collection.style.display === "";
                collection.style.display = isHidden ? "block" : "none";
            }
        });
    }

    if (collection) {
        collection.querySelectorAll(".dropdown-item").forEach(function (item) {
            item.addEventListener("click", function (e) {
                e.preventDefault();
                const action = this.dataset.action;

                switch (action) {
                    case "status":
                        if (typeof openBulkStatusModal === "function")
                            openBulkStatusModal();
                        break;
                    case "delete":
                        if (typeof openBulkDeleteModal === "function")
                            openBulkDeleteModal();
                        break;
                    case "restore":
                        if (typeof openBulkRestoreModal === "function")
                            openBulkRestoreModal();
                        break;
                    case "force_delete":
                        if (typeof openBulkForceDeleteModal === "function")
                            openBulkForceDeleteModal();
                        break;
                    default:
                        console.warn("Unknown action:", action);
                }

                if (collection) collection.style.display = "none";
            });
        });
    }
}
window.attachBulkButtonEvents = attachBulkButtonEvents;

// ─── إغلاق الـ Dropdown عند الضغط خارجها ──────────────────
document.addEventListener("click", function (e) {
    const bulkBtn = document.getElementById("bulkActionsBtn");
    if (!bulkBtn) return;

    const collection = bulkBtn.nextElementSibling;
    if (!collection || !collection.classList.contains("dt-button-collection"))
        return;

    const isClickInside =
        bulkBtn.contains(e.target) || collection.contains(e.target);
    if (!isClickInside) {
        collection.style.display = "none";
    }
});

// ─── حدث الضغط على زر التبديل ──────────────────────────────
document
    .getElementById("toggleTrashed")
    ?.addEventListener("click", function () {
        window.showTrashed = !window.showTrashed;
        const btnText = document.getElementById("trashedBtnText");
        const table = window.LaravelDataTables["menu_items_datatable"];
        if (window.showTrashed) {
            btnText.textContent = window.translations.show_active;
            table.ajax.url(window.routes.index + "?show_trashed=true").load();
        } else {
            btnText.textContent = window.translations.show_trashed;
            table.ajax.url(window.routes.index + "?show_trashed=false").load();
        }

        setTimeout(function () {
            attachBulkButtonEvents();
        }, 500);
    });

// ─── تشغيل الـ attach أول مرة ──────────────────────────────
setTimeout(function () {
    attachBulkButtonEvents();
}, 1000);