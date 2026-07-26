"use strict";

document.addEventListener("DOMContentLoaded", function () {
    const csrfToken = () =>
        document.querySelector('meta[name="csrf-token"]').content;

    const fetchJson = async (url, method, body = {}) => {
        const options = {
            method,
            headers: {
                Accept: "application/json",
                "X-CSRF-TOKEN": csrfToken(),
                "X-Requested-With": "XMLHttpRequest",
            },
        };
        if (method !== "GET") {
            options.headers["Content-Type"] = "application/json";
            options.body = JSON.stringify(body);
        }
        const res = await fetch(url, options);
        return res.json();
    };

    const treeRoot = document.getElementById("treeRoot");
    const availableList = document.getElementById("availableItemsList");
    const emptyPlaceholder = document.getElementById("emptyTreePlaceholder");
    const saveBtn = document.getElementById("saveTreeBtn");

    // ─── تفعيل Sortable على كل الـ <ul> بتاعة الشجرة ────────────
    const sortableInstances = [];

    const initSortableOn = (ul) => {
    const instance = new Sortable(ul, {
        group: "menu-structure",
        animation: 150,
        forceFallback: true,        // ← جديد: يحل مشكلة تحديد النص
        fallbackClass: "sortable-fallback",
        fallbackOnBody: true,
        swapThreshold: 0.65,
        handle: ".drag-handle",
        onStart: () => document.body.classList.add("dragging-active"),
        onEnd: () => document.body.classList.remove("dragging-active"),
        onAdd: handleTreeChange,
        onUpdate: handleTreeChange,
    });
    sortableInstances.push(instance);
};

    const initAllTreeSortables = () => {
        document.querySelectorAll(".tree-list").forEach((ul) => {
            if (ul.dataset.sortableInit) return;
            ul.dataset.sortableInit = "true";
            initSortableOn(ul);
        });
    };

    // ─── قايمة العناصر المتاحة (المصدر بس، منتقلش منها) ────────
    new Sortable(availableList, {
        group: {
            name: "menu-structure",
            pull: "clone",
            put: false,
        },
        sort: false,
        animation: 150,
        forceFallback: true,           // ← جديد
        fallbackOnBody: true,
        handle: ".drag-handle",
        onStart: () => document.body.classList.add("dragging-active"),
        onEnd: () => document.body.classList.remove("dragging-active"),
    });

    initAllTreeSortables();

    // ─── لما عنصر يتسحب من "المتاحة" للشجرة ────────────────────
    async function handleTreeChange(evt) {
        toggleEmptyPlaceholder();

        // ─── لو العنصر جديد (جاي من available list) ────────────
        const item = evt.item;
        if (item.classList.contains("available-item")) {
            const itemId = item.dataset.itemId;
            const parentLi = evt.to.dataset.parentId || null;

            item.remove(); // نشيل الـ clone المؤقت

            try {
                const res = await fetchJson(
                    window.structureRoutes.addNode,
                    "POST",
                    {
                        menu_item_id: itemId,
                        parent_id: parentLi || null,
                    }
                );

                if (res.success) {
                    const node = res.node;
                    const nodeHtml = buildNodeElement(node);
                    const nestedUl = buildNestedUl(node.id);

                    evt.to.insertBefore(nodeHtml, evt.to.children[evt.newIndex] || null);
                    evt.to.insertBefore(nestedUl, nodeHtml.nextSibling);

                    initAllTreeSortables();

                    // ─── نشيل العنصر من قايمة المتاحة الأصلية ────
                    document
                        .querySelector(`.available-item[data-item-id="${itemId}"]`)
                        ?.remove();

                    Alert.success(res.message);
                    persistTree();
                } else {
                    Alert.error(res.message || window.structureTranslations.error);
                    persistTree(); // نرجّع الشكل الصح لو حصل رفض
                }
            } catch (err) {
                console.error(err);
                Alert.error(window.structureTranslations.error);
            }
            return;
        }

        // ─── إعادة ترتيب / نقل عنصر موجود ────────────────────────
        persistTree();
    }

    const buildNodeElement = (node) => {
        const li = document.createElement("li");
        li.className = "tree-node";
        li.dataset.nodeId = node.id;
        li.dataset.menuItemId = node.menu_item_id;
        li.dataset.itemIcon = node.icon || "";
        li.dataset.itemType = node.type;

        const toggleBtn = node.type === "dropdown"
            ? `<button type="button" class="btn btn-sm btn-icon toggle-children-btn">
                <i class="ti ti-chevron-down"></i>
            </button>`
            : "";

        li.innerHTML = `
            <div class="node-content">
                ${toggleBtn}
                <i class="ti ti-grip-vertical drag-handle"></i>
                ${node.icon ? `<i class="${node.icon}"></i>` : ""}
                <span class="node-title">${node.title}</span>
                <span class="badge bg-light text-dark node-badge">${node.type}</span>
            </div>
            <button type="button" class="btn btn-sm btn-icon btn-light-danger btn-remove-node" data-node-id="${node.id}">
                <i class="ti ti-x fs-6"></i>
            </button>
        `;
        return li;
    };

    const buildNestedUl = (nodeId) => {
        const ul = document.createElement("ul");
        ul.className = "tree-list nested";
        ul.dataset.parentId = nodeId;
        return ul;
    };

    const toggleEmptyPlaceholder = () => {
        const hasNodes = treeRoot.querySelector(".tree-node") !== null;
        emptyPlaceholder.classList.toggle("d-none", hasNodes);
    };

    // ─── تحويل شكل الشجرة الحالي في الـ DOM لـ JSON ─────────────
    const serializeTree = (ul) => {
        const result = [];
        Array.from(ul.children).forEach((child) => {
            if (!child.classList.contains("tree-node")) return;

            const nodeId = parseInt(child.dataset.nodeId, 10);
            const nestedUl = child.nextElementSibling;
            const children =
                nestedUl && nestedUl.classList.contains("nested")
                    ? serializeTree(nestedUl)
                    : [];

            result.push({ id: nodeId, children });
        });
        return result;
    };

    const persistTree = async () => {
        const tree = serializeTree(treeRoot);
        try {
            const res = await fetchJson(window.structureRoutes.saveTree, "POST", {
                tree,
            });
            if (!res.success) {
                Alert.error(res.message || window.structureTranslations.error);
            }
        } catch (err) {
            console.error(err);
            Alert.error(window.structureTranslations.error);
        }
    };

    // ─── حفظ يدوي بالزرار ────────────────────────────────────────
    saveBtn.addEventListener("click", async () => {
        const indicatorLabel = saveBtn.querySelector(".indicator-label");
        const indicatorProgress = saveBtn.querySelector(".indicator-progress");
        indicatorLabel.classList.add("d-none");
        indicatorProgress.classList.remove("d-none");
        saveBtn.disabled = true;

        await persistTree();
        Alert.success(window.structureTranslations.saved);

        indicatorLabel.classList.remove("d-none");
        indicatorProgress.classList.add("d-none");
        saveBtn.disabled = false;
    });

    // ─── حذف Node ──────────────────────────────────────────────
    document.addEventListener("click", async (e) => {
        const btn = e.target.closest(".btn-remove-node");
        if (!btn) return;

        if (!confirm(window.structureTranslations.confirmRemove)) return;

        const nodeId = btn.dataset.nodeId;
        const url = window.structureRoutes.removeNode.replace("__ID__", nodeId);

        try {
            const res = await fetch(url, {
                method: "DELETE",
                headers: {
                    Accept: "application/json",
                    "X-CSRF-TOKEN": csrfToken(),
                    "X-Requested-With": "XMLHttpRequest",
                },
            });
            const data = await res.json();

            if (data.success) {
                const li = btn.closest(".tree-node");
                const nestedUl = li.nextElementSibling;
                li.remove();
                if (nestedUl && nestedUl.classList.contains("nested")) {
                    nestedUl.remove(); // بيحذف الأبناء بصريًا (اتحذفوا فعليًا بالـ DB cascade)
                }
                toggleEmptyPlaceholder();
                Alert.success(data.message);
            } else {
                Alert.error(data.message || window.structureTranslations.error);
            }
        } catch (err) {
            console.error(err);
            Alert.error(window.structureTranslations.error);
        }
    });

    // ─── بحث في العناصر المتاحة ──────────────────────────────────
    document.getElementById("searchAvailableItems").addEventListener("keyup", function () {
        const term = this.value.trim().toLowerCase();
        document.querySelectorAll(".available-item").forEach((item) => {
            const text = item.textContent.toLowerCase();
            item.style.display = text.includes(term) ? "flex" : "none";
        });
    });

    toggleEmptyPlaceholder();
});