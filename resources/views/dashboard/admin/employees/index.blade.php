@extends('dashboard.layouts.master')
@push('css')
<style>
    .dt-button-collection {
        border-radius: 10px !important;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15) !important;
        border: 1px solid #e0e0e0 !important;
        min-width: 200px !important;
        z-index: 99999 !important;
        position: absolute !important;
        margin-top: 5px !important;
    }

    /* الـ wrapper بتاع الـ buttons يكون relative */
    .dt-buttons {
        position: relative !important;
    }

    .dataTables_wrapper,
    .card-header,
    .card,
    .table-responsive {
        overflow: visible !important;
    }
</style>
@endpush

@section('title')
{{ $title }}
@endsection

@section('content')
<div class="page-content">
    <div class="content-header">
        <h1 class="mb-0">{{ trans('dashboard/employees.employees') }}</h1>
        <ul class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}">{{ trans('dashboard/header.main_dashboard') }}</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('admin.employees.index') }}">{{ trans('dashboard/employees.employees') }}</a>
            </li>
        </ul>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <span class="nav-icon">
                        <i class="ti ti-users"></i>
                    </span>
                    {{ trans('dashboard/employees.employees') }}
                    <div class="gap-2 d-flex">

                        <button data-pc-animate="3d-sign" type="button" class="btn btn-sm btn-light btn-active-primary"
                            data-bs-toggle="modal" data-bs-target="#createEmployeeModal">
                            <i class="fa fa-plus"></i>
                            {{ trans('dashboard/employees.create') }}
                        </button>
                        <button type="button" class="btn btn-sm btn-danger btn-active-primary" id="toggleTrashed"
                            style="display: none;">
                            <i class="ti ti-trash me-1"></i>
                            <span id="trashedBtnText">{{ trans('dashboard/employees.show_trashed') }}</span>
                        </button>
                    </div>

                    @include('dashboard.admin.employees.btn.create', compact('companies', 'departments'))
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-row-bordered gy-5 gs-7">
                            {!! $dataTable->table() !!}
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
{!! $dataTable->scripts() !!}
<script>
    // ─── تعريف الترجمات والـ Routes ──────────────────────────────
window.translations = {
    error: "{{ trans('dashboard/general.error_occurred') }}",
    restore: "{{ trans('dashboard/employees.restore') }}",
    restore_confirm: "{{ trans('dashboard/employees.restore_confirm') }}",
    force_delete: "{{ trans('dashboard/employees.force_delete') }}",
    force_delete_confirm: "{{ trans('dashboard/employees.force_delete_confirm') }}",
    show_active: "{{ trans('dashboard/employees.show_active') }}",
    show_trashed: "{{ trans('dashboard/employees.show_trashed') }}",
    bulk_select_at_least_one: "{{ trans('dashboard/employees.bulk_select_at_least_one') }}",
    bulk_status_confirm: "{{ trans('dashboard/employees.bulk_status_confirm') }}",
    bulk_delete_confirm: "{{ trans('dashboard/employees.bulk_delete_confirm') }}",
    bulk_change_status: "{{ trans('dashboard/employees.bulk_change_status') }}",
    delete_selected: "{{ trans('dashboard/general.delete_selected') }}",
    confirm: "{{ trans('dashboard/general.confirm') }}",
    delete: "{{ trans('dashboard/general.delete') }}",
    bulk_actions: "{{ trans('dashboard/employees.bulk_actions') }}",
    bulk_restore: "{{ trans('dashboard/employees.bulk_restore') }}",
    bulk_force_delete: "{{ trans('dashboard/employees.bulk_force_delete') }}",
    bulk_restore_confirm: "{{ trans('dashboard/employees.bulk_restore_confirm') }}",
    bulk_force_delete_confirm: "{{ trans('dashboard/employees.bulk_force_delete_confirm') }}",
};

window.routes = {
    index: "{{ route('admin.employees.index') }}",
    edit: "{{ route('admin.employees.edit', ['employee' => '__ID__']) }}",
    update: "{{ route('admin.employees.update', ['employee' => '__ID__']) }}",
    destroy: "{{ route('admin.employees.destroy', ['employee' => '__ID__']) }}",
    restore: "{{ route('admin.employees.restore', ['employee' => '__ID__']) }}",
    forceDelete: "{{ route('admin.employees.forceDelete', ['employee' => '__ID__']) }}",
    hasTrashed: "{{ route('admin.employees.hasTrashed') }}",
    bulkAction: "{{ route('admin.employees.bulkAction') }}",
};

window.showTrashed = false;

// ─── Confirm Modal ──────────────────────────────────────────────────────────
let confirmCallback = null;
const showConfirmModal = (title, message, confirmText = 'تأكيد', callback) => {
    document.getElementById('confirmActionTitle').textContent = title;
    document.getElementById('confirmActionBody').textContent = message;
    const confirmBtn = document.getElementById('confirmActionBtn');
    confirmBtn.querySelector('.indicator-label').textContent = confirmText;
    confirmBtn.classList.remove('btn-success', 'btn-danger', 'btn-primary');
    confirmBtn.classList.add('btn-danger');
    confirmCallback = callback;
    const modal = new bootstrap.Modal(document.getElementById('confirmActionModal'));
    modal.show();
};

document.addEventListener('click', async function(e) {
    const confirmBtn = e.target.closest('#confirmActionBtn');
    if (!confirmBtn) return;
    if (typeof confirmCallback === 'function') {
        if (typeof setBtnLoading === 'function') {
            setBtnLoading(confirmBtn, true);
            await confirmCallback(confirmBtn);
            setBtnLoading(confirmBtn, false);
        } else {
            await confirmCallback(confirmBtn);
        }
    }
    const modal = bootstrap.Modal.getInstance(document.getElementById('confirmActionModal'));
    if (modal) modal.hide();
    confirmCallback = null;
});

// ─── تعريف المتغيرات العالمية ──────────────────────────────
var bulkActionData = null;
var bulkActionType = null;

// ─── دالة جلب أسماء الموظفين المختارين ──────────────────
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

// ─── Bulk Restore Modal ──────────────────────────────────────
function openBulkRestoreModal() {
    const checkboxes = document.querySelectorAll(".row-checkbox:checked");
    const ids = Array.from(checkboxes).map((cb) => parseInt(cb.value));
    if (ids.length === 0) {
        Alert.warning(window.translations.bulk_select_at_least_one);
        return;
    }
    const employeeNames = getSelectedEmployeeNames(checkboxes);
    document.getElementById("bulkActionModalTitle").textContent = window.translations.bulk_restore || "استعادة جماعية";
    document.getElementById("bulkActionMessage").textContent = window.translations.bulk_restore_confirm || "هل أنت متأكد من استعادة الموظفين المحددين؟";
    document.getElementById("bulkStatusDropdown").style.display = "none";
    document.getElementById("bulkActionEmployeeList").innerHTML = employeeNames;
    bulkActionData = { ids: ids };
    bulkActionType = "restore";
    const confirmBtn = document.getElementById("confirmBulkAction");
    confirmBtn.className = "btn btn-success";
    confirmBtn.querySelector(".indicator-label").textContent = window.translations.restore || "استعادة";
    const modal = new bootstrap.Modal(document.getElementById("bulkActionModal"));
    modal.show();
}
window.openBulkRestoreModal = openBulkRestoreModal;

// ─── Bulk Force Delete Modal ──────────────────────────────────
function openBulkForceDeleteModal() {
    const checkboxes = document.querySelectorAll(".row-checkbox:checked");
    const ids = Array.from(checkboxes).map((cb) => parseInt(cb.value));
    if (ids.length === 0) {
        Alert.warning(window.translations.bulk_select_at_least_one);
        return;
    }
    const employeeNames = getSelectedEmployeeNames(checkboxes);
    document.getElementById("bulkActionModalTitle").textContent = window.translations.bulk_force_delete || "حذف نهائي جماعي";
    document.getElementById("bulkActionMessage").textContent = window.translations.bulk_force_delete_confirm || "تحذير! هذا الإجراء لا يمكن التراجع عنه. هل أنت متأكد؟";
    document.getElementById("bulkStatusDropdown").style.display = "none";
    document.getElementById("bulkActionEmployeeList").innerHTML = employeeNames;
    bulkActionData = { ids: ids };
    bulkActionType = "force_delete";
    const confirmBtn = document.getElementById("confirmBulkAction");
    confirmBtn.className = "btn btn-danger";
    confirmBtn.querySelector(".indicator-label").textContent = window.translations.force_delete || "حذف نهائي";
    const modal = new bootstrap.Modal(document.getElementById("bulkActionModal"));
    modal.show();
}
window.openBulkForceDeleteModal = openBulkForceDeleteModal;

// ─── دالة ربط الأحداث بالأزرار ──────────────────────────────
function attachBulkButtonEvents() {
    const bulkBtn = document.getElementById('bulkActionsBtn');
    if (!bulkBtn) return;

    // ─── نشيل الأحداث القديمة ──────────────────────────────
    const newBtn = bulkBtn.cloneNode(true);
    bulkBtn.parentNode.replaceChild(newBtn, bulkBtn);

    // ─── نجيب الـ Dropdown القديم ونشيله ────────────────────
    let oldCollection = newBtn.nextElementSibling;
    if (oldCollection && oldCollection.classList.contains('dt-button-collection')) {
        oldCollection.remove();
    }

    let dropdownHTML = `
    <div class="dt-button-collection" style="display: none; position: absolute; top: 100%; right: 0; z-index: 99999; min-width: 200px; margin-top: 5px; background: #fff; border-radius: 10px; box-shadow: 0 8px 24px rgba(0,0,0,0.15); border: 1px solid #e0e0e0; padding: 8px 0;">
    `;

    if (window.showTrashed) {
        dropdownHTML += `
        <a class="dropdown-item text-success" href="#" data-action="restore" style="padding: 10px 24px; display: block; text-decoration: none; color: #28a745; transition: all 0.2s ease; cursor: pointer; font-size: 14px;">
            <i class="ti ti-refresh me-2 text-success"></i> ${window.translations.bulk_restore || 'استعادة جماعية'}
        </a>
        <a class="dropdown-item text-danger" href="#" data-action="force_delete" style="padding: 10px 24px; display: block; text-decoration: none; color: #dc3545; transition: all 0.2s ease; cursor: pointer; font-size: 14px;">
            <i class="ti ti-trash-off me-2 text-danger"></i> ${window.translations.bulk_force_delete || 'حذف نهائي جماعي'}
        </a>
        `;
    } else {
        dropdownHTML += `
        <a class="dropdown-item" href="#" data-action="status" style="padding: 10px 24px; display: block; text-decoration: none; color: #333; transition: all 0.2s ease; cursor: pointer; font-size: 14px;">
            <i class="ti ti-exchange me-2 text-primary"></i> ${window.translations.bulk_change_status || 'تغيير الحالة'}
        </a>
        <a class="dropdown-item text-danger" href="#" data-action="delete" style="padding: 10px 24px; display: block; text-decoration: none; color: #dc3545; transition: all 0.2s ease; cursor: pointer; font-size: 14px;">
            <i class="ti ti-trash me-2 text-danger"></i> ${window.translations.delete_selected || 'حذف المحدد'}
        </a>
        `;
    }

    dropdownHTML += `</div>`;
    newBtn.insertAdjacentHTML('afterend', dropdownHTML);

    const finalBtn = document.getElementById('bulkActionsBtn');
    const collection = finalBtn?.nextElementSibling;

    if (finalBtn) {
        finalBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            e.preventDefault();
            if (collection) {
                const isHidden = collection.style.display === 'none' || collection.style.display === '';
                collection.style.display = isHidden ? 'block' : 'none';
            }
        });
    }

    if (collection) {
        collection.querySelectorAll('.dropdown-item').forEach(function(item) {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                const action = this.dataset.action;

                switch(action) {
                    case 'status':
                        if (typeof openBulkStatusModal === "function") openBulkStatusModal();
                        break;
                    case 'delete':
                        if (typeof openBulkDeleteModal === "function") openBulkDeleteModal();
                        break;
                    case 'restore':
                        if (typeof openBulkRestoreModal === "function") openBulkRestoreModal();
                        break;
                    case 'force_delete':
                        if (typeof openBulkForceDeleteModal === "function") openBulkForceDeleteModal();
                        break;
                    default:
                        console.warn('Unknown action:', action);
                }

                if (collection) collection.style.display = 'none';
            });
        });
    }
}

document.addEventListener('click', function(e) {
    const bulkBtn = document.getElementById('bulkActionsBtn');
    if (!bulkBtn) return;

    const collection = bulkBtn.nextElementSibling;
    if (!collection || !collection.classList.contains('dt-button-collection')) return;

    const isClickInside = bulkBtn.contains(e.target) || collection.contains(e.target);
    if (!isClickInside) {
        collection.style.display = 'none';
    }
});

document.getElementById('toggleTrashed')?.addEventListener('click', function() {
    window.showTrashed = !window.showTrashed;
    const btnText = document.getElementById('trashedBtnText');
    const table = window.LaravelDataTables['employees_datatable'];
    if (window.showTrashed) {
        btnText.textContent = window.translations.show_active;
        table.ajax.url(window.routes.index + '?show_trashed=true').load();
    } else {
        btnText.textContent = window.translations.show_trashed;
        table.ajax.url(window.routes.index + '?show_trashed=false').load();
    }

    setTimeout(function() {
        attachBulkButtonEvents();
    }, 500);
});

setTimeout(function() {
    attachBulkButtonEvents();
}, 1000);
</script>

<script src="{{ asset('dashboard/themes/'. $theme_code .'/assets/js/custom/utils/alert.js') }}"></script>
<script
    src="{{ asset('dashboard/themes/'. $theme_code .'/assets/js/custom/admin/employees/index.js') }}?v={{ time() }}">
</script>
@endpush
