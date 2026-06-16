class Alert {
    static show(message, type = "success") {
        const icons = {
            success: "fa-check-circle",
            error: "fa-times-circle",
            warning: "fa-exclamation-triangle",
            info: "fa-info-circle",
        };

        const alert = $(`
            <div class="custom-alert ${type}">
                <i class="fa ${icons[type]}"></i>
                ${message}
            </div>
        `);

        $("body").append(alert);

        setTimeout(() => {
            alert.addClass("fade-out");
            setTimeout(() => alert.remove(), 600);
        }, 4000);
    }

    static success(message) {
        this.show(message, "success");
    }
    static error(message) {
        this.show(message, "error");
    }
    static warning(message) {
        this.show(message, "warning");
    }
    static info(message) {
        this.show(message, "info");
    }
}