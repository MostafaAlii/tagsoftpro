"use strict";

window.ItemPanel = (function () {
    const csrfToken = () =>
        document.querySelector('meta[name="csrf-token"]').content;

    const fetchJson = async (url, method = "GET", body = null) => {
        const options = {
            method,
            headers: {
                Accept: "application/json",
                "X-CSRF-TOKEN": csrfToken(),
                "X-Requested-With": "XMLHttpRequest",
            },
        };
        if (body !== null) {
            options.headers["Content-Type"] = "application/json";
            options.body = JSON.stringify(body);
        }
        const res = await fetch(url, options);
        return res.json();
    };

    const route = (name, id) => window.panelRoutes[name].replace("__ID__", id);

    // ─── الحالة العامة ───────────────────────────────────────────
    const state = {
        page: 1,
        loading: false,
        hasMore: true,
        search: "",
        trashed: false,
        selectedIds: new Set(),
    };

    const els = {};

    const initEls = () => {
        els.list = document.getElementById("availableItemsList");
        els.loader = document.getElementById("panelLoader");
        els.emptyMsg = document.getElementById("panelEmptyMsg");
        els.loadMoreBtn = document.getElementById("btnLoadMore");
        els.searchInput = document.getElementById("searchAvailableItems");
        els.bulkBar = document.getElementById("panelBulkBar");
        els.selectedCount = document.getElementById("panelSelectedCount");
        els.trashedToggleBtn = document.getElementById("btnTogglePanelTrashed");
        els.trashedToggleText = document.getElementById("panelTrashedBtnText");
        els.createBtn = document.getElementById("btnCreatePanelItem");
        els.panelTitle = document.getElementById("panelTitle");

        els.bulkChangeStatusBtn = document.getElementById("btnBulkChangeStatus");
        els.bulkDeleteBtn = document.getElementById("btnBulkDelete");
        els.bulkRestoreBtn = document.getElementById("btnBulkRestore");
        els.bulkForceDeleteBtn = document.getElementById("btnBulkForceDelete");
    };

    // ─── بناء عنصر li (active أو trashed) ──────────────────────
    const buildItemLi = (item) => {
    const li = document.createElement("li");
    li.className = "available-item";
    li.dataset.itemId = item.id;
    li.dataset.itemType = item.type;
    li.style.cssText = "display:flex;align-items:center;justify-content:space-between;gap:8px;padding:8px 10px;background:#fff;border:1px solid #e2e5ea;border-radius:8px;margin-bottom:6px;";

    const iconHtml = item.icon ? `<i class="${item.icon}" style="color:#6c5ce7;font-size:16px;"></i>` : "";

    if (state.trashed) {
        li.innerHTML = `
            <div style="display:flex;align-items:center;gap:6px;flex:1 1 auto;min-width:0;overflow:hidden;">
                <input type="checkbox" class="item-checkbox" data-item-id="${item.id}" style="cursor:pointer;">
                ${iconHtml}
                <span class="node-title" style="color:#2d3436;font-size:14px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">${item.title}</span>
                <span style="font-size:11px;padding:2px 8px;border-radius:20px;background:#f1f1f1;color:#333;flex-shrink:0;">${item.type}</span>
            </div>
            <div style="display:flex;align-items:center;gap:4px;flex-shrink:0;">
                <button type="button" class="btn btn-sm btn-icon btn-light-success btn-item-restore" data-id="${item.id}" title="${window.panelTranslations.restore_label}">
                    <i class="ti ti-refresh fs-6"></i>
                </button>
                <button type="button" class="btn btn-sm btn-icon btn-light-danger btn-item-force-delete" data-id="${item.id}" title="${window.panelTranslations.force_delete_label}">
                    <i class="ti ti-trash-off fs-6"></i>
                </button>
            </div>
        `;
    } else {
        li.innerHTML = `
            <div style="display:flex;align-items:center;gap:6px;flex:1 1 auto;min-width:0;overflow:hidden;">
                <input type="checkbox" class="item-checkbox" data-item-id="${item.id}" style="cursor:pointer;flex-shrink:0;">
                <i class="ti ti-grip-vertical drag-handle" style="cursor:grab;color:#6c5ce7;font-size:18px;flex-shrink:0;"></i>
                ${iconHtml}
                <span class="node-title" style="color:#2d3436;font-size:14px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">${item.title}</span>
                <span style="font-size:11px;padding:2px 8px;border-radius:20px;background:#f1f1f1;color:#333;flex-shrink:0;">${item.type}</span>
            </div>
            <div style="display:flex;align-items:center;gap:6px;flex-shrink:0;">
                <div style="display:flex;align-items:center;gap:2px;padding:2px 6px;border-radius:16px;background:#eef0ff;border:1px solid #d8dcff;" title="${window.panelTranslations.status_label}">
                    <i class="ti ti-power" style="font-size:12px;color:#6c5ce7;"></i>
                    <input type="checkbox" class="toggle-item-status" data-id="${item.id}" ${item.status === "active" ? "checked" : ""} style="width:24px;height:14px;cursor:pointer;margin:0;">
                </div>
                <div style="display:flex;align-items:center;gap:2px;padding:2px 6px;border-radius:16px;background:#fff4e0;border:1px solid #ffe1a8;" title="${window.panelTranslations.is_owner_only_label}">
                    <i class="ti ti-crown" style="font-size:12px;color:#e6a300;"></i>
                    <input type="checkbox" class="toggle-item-owner" data-id="${item.id}" ${item.is_owner_only ? "checked" : ""} style="width:24px;height:14px;cursor:pointer;margin:0;">
                </div>
                <button type="button" class="btn btn-sm btn-icon btn-light-warning btn-item-edit" data-id="${item.id}" style="width:28px;height:28px;padding:0;">
                    <i class="ti ti-edit fs-6"></i>
                </button>
                <button type="button" class="btn btn-sm btn-icon btn-light-danger btn-item-delete" data-id="${item.id}" style="width:28px;height:28px;padding:0;">
                    <i class="ti ti-trash fs-6"></i>
                </button>
            </div>
        `;
    }

    return li;
};

    // ─── تحميل صفحة من العناصر ────────────────────────────────
    const loadPage = async (reset = false) => {
        if (state.loading) return;
        if (reset) {
            state.page = 1;
            state.hasMore = true;
            els.list.innerHTML = "";
            state.selectedIds.clear();
            updateBulkBar();
        }
        if (!state.hasMore) return;

        state.loading = true;
        els.loader.classList.remove("d-none");
        els.loadMoreBtn.classList.add("d-none");

        const params = new URLSearchParams({
            page: state.page,
            trashed: state.trashed ? "true" : "false",
            search: state.search,
        });
        if (!state.trashed) params.append("menu_id", window.panelMenuId);

        try {
            const res = await fetchJson(`${window.panelRoutes.list}?${params.toString()}`);
            if (res.success) {
                res.items.forEach((item) => {
                    els.list.appendChild(buildItemLi(item));
                });
                state.hasMore = res.has_more;
                state.page = res.next_page;

                els.emptyMsg.classList.toggle("d-none", els.list.children.length > 0);
                els.loadMoreBtn.classList.toggle("d-none", !state.hasMore);
            }
        } catch (err) {
            console.error(err);
        } finally {
            state.loading = false;
            els.loader.classList.add("d-none");
        }
    };

    // ─── تحديث شريط الإجراءات الجماعية ──────────────────────────
    const updateBulkBar = () => {
        const count = state.selectedIds.size;
        els.selectedCount.textContent = count;
        els.bulkBar.classList.toggle("d-none", count === 0);
        els.bulkBar.classList.toggle("d-flex", count > 0);

        els.bulkChangeStatusBtn.classList.toggle("d-none", state.trashed);
        els.bulkDeleteBtn.classList.toggle("d-none", state.trashed);
        els.bulkRestoreBtn.classList.toggle("d-none", !state.trashed);
        els.bulkForceDeleteBtn.classList.toggle("d-none", !state.trashed);
    };

    // ─── التحقق من وجود عناصر محذوفة (لإظهار زرار المحذوفات) ────
    const checkHasTrashed = async () => {
        try {
            const res = await fetchJson(window.panelRoutes.hasTrashed);
            els.trashedToggleBtn.style.display = res.hasTrashed
                ? "inline-block"
                : (state.trashed ? "inline-block" : "none");
        } catch (err) {
            console.error(err);
        }
    };

    // ─── تبديل بين active / trashed ─────────────────────────────
    const toggleTrashedMode = () => {
        state.trashed = !state.trashed;
        state.search = "";
        els.searchInput.value = "";

        els.trashedToggleText.textContent = state.trashed
            ? window.panelTranslations.show_active_label
            : window.panelTranslations.show_trashed_label;

        els.trashedToggleBtn.classList.toggle("btn-light-danger", !state.trashed);
        els.trashedToggleBtn.classList.toggle("btn-light-success", state.trashed);

        els.createBtn.classList.toggle("d-none", state.trashed);

        els.panelTitle.textContent = state.trashed
            ? window.panelTranslations.show_trashed_label
            : window.panelTranslations.menu_items_title;

        loadPage(true);
    };

    document.addEventListener("DOMContentLoaded", () => {
        initEls();
        loadPage(true);
        checkHasTrashed();

        els.loadMoreBtn.addEventListener("click", () => loadPage(false));

        let searchTimeout;
        els.searchInput.addEventListener("keyup", () => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                state.search = els.searchInput.value.trim();
                loadPage(true);
            }, 350);
        });

        els.trashedToggleBtn.addEventListener("click", toggleTrashedMode);

        // ─── تحديد/إلغاء تحديد عنصر ──────────────────────────────
        document.addEventListener("change", (e) => {
            const cb = e.target.closest(".item-checkbox");
            if (!cb) return;

            const id = cb.dataset.itemId;
            if (cb.checked) {
                state.selectedIds.add(id);
            } else {
                state.selectedIds.delete(id);
            }
            updateBulkBar();
        });
    });

    return {
        state,
        els: () => els,
        loadPage,
        updateBulkBar,
        checkHasTrashed,
        fetchJson,
        route,
        buildItemLi,
    };
})();
