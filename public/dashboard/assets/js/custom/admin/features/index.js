$(document).ready(function () {
    $(document).on("submit", "form", function (e) {
        if ($(this).attr("id") === "editForm") {
            e.preventDefault();
            e.stopPropagation();
            return false;
        }
    });
});

let currentEditId = null;

function getRoute(routeName, id) {
    if (!window.routes || !window.routes[routeName]) {
        console.error(`Route ${routeName} not found`, window.routes);
        return null;
    }
    return window.routes[routeName].replace("__ID__", id);
}

function showModalLoading(show) {
    if (show) {
        if (!$("#modal-loader").length) {
            $("#editFeatureModal .modal-content").css("position", "relative")
                .append(`
                <div id="modal-loader" style="position:absolute;top:0;left:0;right:0;bottom:0;background:rgba(255,255,255,0.9);z-index:9999;display:flex;align-items:center;justify-content:center;border-radius:8px;">
                    <div class="text-center">
                        <i class="fa fa-spinner fa-spin fa-3x fa-fw"></i>
                        <div class="mt-2">جاري تحميل البيانات...</div>
                    </div>
                </div>
            `);
        }
    } else {
        $("#modal-loader").remove();
        $("#editFeatureModal .modal-content").css("position", "");
    }
}

function fillEditForm(data) {
    if (data.translations && Array.isArray(data.translations)) {
        window.locales.forEach((locale) => {
            const translation = data.translations.find(
                (t) => t.locale === locale,
            );
            $(`#edit_name_${locale}`).val(translation?.name || "");
        });
    }

    $("#edit_key").val(data.key || "");
    $("#edit_type").val(data.type || "ui");
    $("#edit_scope").val(data.scope || "main");

    const isActive = data.status == 1;
    $("#editStatusSwitch").prop("checked", isActive);
    $("#editStatusLabel").text(
        isActive ? window.translations.active : window.translations.inactive,
    );

    const updateUrl = getRoute("update", data.id);
    if (updateUrl) $("#editForm").attr("action", updateUrl);
}

function resetEditForm() {
    $("#editForm")[0]?.reset();
    $("#editForm").attr("action", "");
    currentEditId = null;

    if (window.locales) {
        window.locales.forEach((locale) => $(`#edit_name_${locale}`).val(""));
    }

    $("#edit_key").val("");
    $("#edit_type").val("ui");
    $("#edit_scope").val("main");
    $("#editStatusSwitch").prop("checked", false);
    $("#editStatusLabel").text(window.translations?.inactive || "Inactive");
}

// ========================
// 1. EDIT
// ========================
$(document).on("click", ".btn-edit-feature", function (e) {
    e.preventDefault();
    currentEditId = $(this).data("id");
    const editUrl = getRoute("edit", currentEditId);

    if (!editUrl) {
        Alert.error("حدث خطأ: رابط التعديل غير موجود");
        return;
    }

    $("#editFeatureModal").modal("show");
    showModalLoading(true);

    $.ajax({
        url: editUrl,
        method: "GET",
        headers: {
            Accept: "application/json",
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (response) {
            if (response.success && response.data) {
                fillEditForm(response.data);
            } else {
                Alert.error("البيانات غير صالحة");
            }
        },
        error: function (xhr) {
            let msg = "حدث خطأ في جلب البيانات";
            if (xhr.status === 404) msg = "العنصر غير موجود";
            else if (xhr.status === 500) msg = "خطأ في السيرفر";
            else if (xhr.responseJSON?.message) msg = xhr.responseJSON.message;
            Alert.error(msg);
            $("#editFeatureModal").modal("hide");
        },
        complete: function () {
            showModalLoading(false);
        },
    });
});

$(document).on("click", '#editForm button[type="submit"]', function (e) {
    e.preventDefault();
    e.stopPropagation();

    const form = $("#editForm");
    const updateUrl = form.attr("action");

    if (!updateUrl || !currentEditId) {
        Alert.error("حدث خطأ في تحديد العنصر");
        return;
    }

    const formData = new FormData(form[0]);
    formData.set("_method", "PUT");

    const submitBtn = $(this);
    const originalText = submitBtn.html();
    submitBtn
        .prop("disabled", true)
        .html('<i class="fa fa-spinner fa-spin"></i> جاري الحفظ...');

    $.ajax({
        url: updateUrl,
        method: "POST",
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            Accept: "application/json",
        },
        success: function (response) {
            if (response.success) {
                Alert.success(response.message);
                $("#editFeatureModal").modal("hide");
                window.LaravelDataTables?.["features_datatable"]?.ajax.reload(
                    null,
                    false,
                );
                resetEditForm();
            } else {
                Alert.error(response.message || window.translations?.error);
            }
        },
        error: function (xhr) {
            let msg = window.translations?.error || "حدث خطأ";
            if (xhr.status === 422 && xhr.responseJSON?.errors) {
                msg = Object.values(xhr.responseJSON.errors).flat().join("\n");
            } else if (xhr.responseJSON?.message) {
                msg = xhr.responseJSON.message;
            }
            Alert.error(msg);
        },
        complete: function () {
            submitBtn.prop("disabled", false).html(originalText);
        },
    });

    return false;
});

$(document).on("change", "#editStatusSwitch", function () {
    $("#editStatusLabel").text(
        $(this).is(":checked")
            ? window.translations.active
            : window.translations.inactive,
    );
});

$("#editFeatureModal").on("hidden.bs.modal", function () {
    resetEditForm();
    showModalLoading(false);
});

// ========================
// 2. TOGGLE STATUS
// ========================
document.addEventListener("change", function (e) {
    if (!e.target.matches(".toggle-status")) return;

    const toggle = e.target;
    const route = toggle.dataset.route;
    const badgeEl = toggle.closest(".d-flex").querySelector(".badge-status");

    fetch(route, {
        method: "PATCH",
        headers: {
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')
                .content,
            Accept: "application/json",
        },
    })
        .then((res) => res.json())
        .then((data) => {
            if (data.success) {
                badgeEl.innerHTML = data.badge;
                Alert.success(data.message);
            } else {
                toggle.checked = !toggle.checked;
                Alert.error(data.message);
            }
        })
        .catch(() => {
            toggle.checked = !toggle.checked;
            Alert.error(window.translations.error);
        });
});

document.addEventListener("change", function (e) {
    if (!e.target.matches(".toggle-scope")) return;

    const toggle = e.target;
    const route = toggle.dataset.route;
    const badgeEl = toggle.closest(".d-flex").querySelector(".badge-status");

    fetch(route, {
        method: "PATCH",
        headers: {
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')
                .content,
            Accept: "application/json",
        },
    })
        .then((res) => res.json())
        .then((data) => {
            if (data.success) {
                badgeEl.innerHTML = data.badge;
                Alert.success(data.message);
            } else {
                toggle.checked = !toggle.checked;
                Alert.error(data.message);
            }
        })
        .catch(() => {
            toggle.checked = !toggle.checked;
            Alert.error(window.translations.error);
        });
});

// ========================
// 3. DELETE
// ========================
let deleteId = null;

$(document).on("click", ".btn-delete-feature", function (e) {
    e.preventDefault();
    deleteId = $(this).data("id");
    const name = $(this).data("name");
    $("#deleteMessage").text(`هل أنت متأكد من حذف "${name}"؟`);
    $("#deleteFeatureModal").modal("show");
});

$(document).on("click", "#confirmDeleteBtn", function (e) {
    e.preventDefault();

    if (!deleteId) {
        Alert.error("لا يوجد عنصر للحذف");
        return;
    }

    const deleteUrl = getRoute("destroy", deleteId);
    if (!deleteUrl) {
        Alert.error("حدث خطأ: رابط الحذف غير موجود");
        return;
    }

    const deleteBtn = $(this);
    const originalText = deleteBtn.html();
    deleteBtn
        .prop("disabled", true)
        .html('<i class="fa fa-spinner fa-spin"></i> جاري الحذف...');

    $.ajax({
        url: deleteUrl,
        method: "DELETE",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            Accept: "application/json",
        },
        success: function (response) {
            if (response.success) {
                Alert.success(response.message);
                $("#deleteFeatureModal").modal("hide");
                window.LaravelDataTables?.["features_datatable"]?.ajax.reload(
                    null,
                    false,
                );
                deleteId = null;
            } else {
                Alert.error(response.message || window.translations?.error);
            }
        },
        error: function (xhr) {
            let msg = window.translations?.error || "حدث خطأ في الحذف";
            if (xhr.status === 404) msg = "العنصر غير موجود";
            else if (xhr.status === 500) msg = "خطأ في السيرفر";
            else if (xhr.responseJSON?.message) msg = xhr.responseJSON.message;
            Alert.error(msg);
        },
        complete: function () {
            deleteBtn.prop("disabled", false).html(originalText);
        },
    });
});

$("#deleteFeatureModal").on("hidden.bs.modal", function () {
    deleteId = null;
    $("#deleteMessage").text("");
});

// ========================
// 4. CREATE STATUS LABEL
// ========================
document
    .getElementById("isActiveSwitch")
    ?.addEventListener("change", function () {
        document.getElementById("isActiveLabel").textContent = this.checked
            ? window.translations.active
            : window.translations.inactive;
    });
