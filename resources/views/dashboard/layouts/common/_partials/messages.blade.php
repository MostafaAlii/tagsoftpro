<style>
    .custom-alert {
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        border-radius: 12px;
        color: #fff;
        font-weight: 500;
        font-size: 15px;
        display: flex;
        align-items: center;
        gap: 10px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.25);
        z-index: 9999;
        opacity: 0;
        animation: slideIn 0.5s forwards, shake 0.6s ease-in-out 0.6s;
        overflow: hidden;
    }

    .custom-alert i {
        font-size: 20px;
        color: #fff;
    }

    .custom-alert.success {
        background: linear-gradient(135deg, #28a745, #34ce57);
    }

    .custom-alert.error {
        background: linear-gradient(135deg, #dc3545, #e4606d);
    }

    .custom-alert.warning {
        background: linear-gradient(135deg, #ffc107, #ffcd39);
        color: #000;
    }

    .custom-alert.warning i {
        color: #000;
    }

    .custom-alert.info {
        background: linear-gradient(135deg, #17a2b8, #1fc8d5);
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateX(120%);
        }

        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    /* خروج بسحب لأعلى */
    @keyframes fadeOutUp {
        from {
            opacity: 1;
            transform: translateY(0);
        }

        to {
            opacity: 0;
            transform: translateY(-50px);
        }
    }

    .fade-out {
        animation: fadeOutUp 0.6s ease forwards;
    }

    /* progress bar */
    .custom-alert::after {
        content: "";
        position: absolute;
        bottom: 0;
        left: 0;
        height: 4px;
        width: 100%;
        background: rgba(255, 255, 255, 0.4);
        animation: progress 4s linear forwards;
    }

    @keyframes progress {
        from {
            width: 100%;
        }

        to {
            width: 0;
        }
    }

    /* اهتزاز */
    @keyframes shake {

        0%,
        100% {
            transform: translateX(0);
        }

        20% {
            transform: translateX(-5px);
        }

        40% {
            transform: translateX(5px);
        }

        60% {
            transform: translateX(-5px);
        }

        80% {
            transform: translateX(5px);
        }
    }
</style>

@if(session('success'))
<div class="custom-alert success">
    <i class="fa fa-check-circle"></i>
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="custom-alert error">
    <i class="fa fa-times-circle"></i>
    {{ session('error') }}
</div>
@endif

@if(session('warning'))
<div class="custom-alert warning">
    <i class="fa fa-exclamation-triangle"></i>
    {{ session('warning') }}
</div>
@endif

@if(session('info'))
<div class="custom-alert info">
    <i class="fa fa-info-circle"></i>
    {{ session('info') }}
</div>
@endif

<script>
    setTimeout(() => {
        document.querySelectorAll('.custom-alert').forEach(alert => {
            alert.classList.add('fade-out');
            setTimeout(() => alert.remove(), 600);
        });
    }, 4000);
</script>
