{{-- Generic Confirm Modal (لحذف/استرجاع/حذف نهائي - سواء فردي أو جماعي) --}}
<div class="modal fade" id="panelConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger" id="panelConfirmTitle">{{ trans('dashboard/general.confirm') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="panelConfirmBody"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{
                    trans('dashboard/general.cancel') }}</button>
                <button type="button" class="btn btn-danger" id="panelConfirmBtn">
                    <span class="indicator-label">{{ trans('dashboard/general.confirm') }}</span>
                    <span class="indicator-progress d-none">
                        <span class="spinner-border spinner-border-sm me-1"></span>{{ trans('dashboard/general.loading')
                        }}
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Bulk Status Modal --}}
<div class="modal fade" id="panelBulkStatusModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ trans('dashboard/menu_items.bulk_change_status') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="mb-2 fw-bold">{{ trans('dashboard/menu_items.selected_items') }}:</p>
                <ul id="panelBulkStatusItemsList" class="mb-3 ps-3"></ul>
                <label class="form-label">{{ trans('dashboard/menu_items.select_status') }}</label>
                <select class="form-select" id="panelBulkStatusSelect">
                    <option value="active">{{ trans('dashboard/menu_items.status_active') }}</option>
                    <option value="inactive">{{ trans('dashboard/menu_items.status_inactive') }}</option>
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{
                    trans('dashboard/general.cancel') }}</button>
                <button type="button" class="btn btn-primary" id="panelBulkStatusConfirmBtn">
                    <span class="indicator-label">{{ trans('dashboard/general.confirm') }}</span>
                    <span class="indicator-progress d-none">
                        <span class="spinner-border spinner-border-sm me-1"></span>{{ trans('dashboard/general.loading')
                        }}
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>