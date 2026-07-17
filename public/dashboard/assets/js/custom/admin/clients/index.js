class CompanyStatus {
    constructor() {
        this.token = $('meta[name="csrf-token"]').attr("content");
        $.ajaxSetup({
            headers: { "X-CSRF-TOKEN": this.token },
        });
        this.init();
    }

    init() {
        this.handleStatusChange();
    }

    handleStatusChange() {
        $(document).on("change", ".company-status-select", (e) => {
            const select = $(e.currentTarget);
            this.updateStatus(select);
        });
    }

    updateStatus(select) {
        const route = select.data("route");
        const rowId = select.data("row-id");
        const status = select.val();

        $.ajax({
            url: route,
            method: "POST",
            data: { _method: "PATCH", status: status },
            success: (response) => this.onSuccess(response, rowId, select),
            error: (xhr) => this.onError(xhr),
        });
    }

    onSuccess(response, rowId, select) {
        if (!response.success) return;

        $("#" + rowId)
            .removeClass("table-success table-warning table-danger")
            .addClass(response.rowClass);

        const modal = select.closest(".modal");
        if (modal.length) {
            const modalInstance = bootstrap.Modal.getInstance(modal[0]);
            if (modalInstance) {
                modal[0].addEventListener(
                    "hidden.bs.modal",
                    () => {
                        Alert.success(response.message);
                    }, { once: true },
                );
                modalInstance.hide();
            } else {
                Alert.success(response.message);
            }
        } else {
            Alert.success(response.message);
        }
    }

    onError(xhr) {
        if (xhr.status === 422) {
            Alert.error(Object.values(xhr.responseJSON.errors)[0][0]);
        } else {
            Alert.error(window.translations.error);
        }
    }
}

$(document).ready(() => new CompanyStatus());