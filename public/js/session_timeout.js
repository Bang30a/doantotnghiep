document.addEventListener("DOMContentLoaded", function () {
    if (window.location.pathname.includes('/login') || window.location.pathname.includes('/register')) {
        return; 
    }
    const metaTimeout = document.querySelector('meta[name="session-timeout"]');

    let sessionMinutes = metaTimeout && metaTimeout.getAttribute('content')
        ? parseInt(metaTimeout.getAttribute('content'))
        : 60;

    console.log("Session timeout JS:", sessionMinutes + " phut");

    if (isNaN(sessionMinutes) || sessionMinutes < 2) {
        sessionMinutes = 2;
    }
    if (window.location.pathname.includes('/login') || 
        window.location.pathname.includes('/register') || 
        window.location.pathname.includes('/forgot-password') || 
        window.location.pathname.includes('/reset-password')) {
        return; 
    }
    const timeoutMilliseconds = sessionMinutes * 60 * 1000;
    const warningTime = sessionMinutes <= 2 ? 30 * 1000 : 60 * 1000;
    const jsTimeout = timeoutMilliseconds - 10000;

    let idleTimer = null;
    let warningTimer = null;
    let timerInterval = null;
    let popupShowing = false;

    const refreshCsrfUrl = window.refreshCsrfUrl || "/refresh-csrf";
    const loginUrl = window.loginUrl || "/login";
    const autoLogoutUrl = window.autoLogoutUrl || "/auto-logout";

    function updateCsrfToken(newToken) {
        if (!newToken) return;

        const meta = document.querySelector('meta[name="csrf-token"]');

        if (meta) {
            meta.setAttribute("content", newToken);
        }

        document.querySelectorAll('input[name="_token"]').forEach(function (input) {
            input.value = newToken;
        });
    }

    async function refreshSession() {
        const response = await fetch(refreshCsrfUrl, {
            method: "GET",
            credentials: "same-origin",
            cache: "no-store",
            headers: {
                "Accept": "application/json",
                "X-Requested-With": "XMLHttpRequest"
            }
        });

        if (!response.ok) {
            throw new Error("Không thể gia hạn phiên đăng nhập");
        }

        const data = await response.json();

        if (!data.success) {
            throw new Error("Gia hạn thất bại");
        }

        updateCsrfToken(data.csrf_token);

        return data;
    }

    function clearAllTimers() {
        clearTimeout(idleTimer);
        clearTimeout(warningTimer);
        clearInterval(timerInterval);

        idleTimer = null;
        warningTimer = null;
        timerInterval = null;
    }

    function resetIdleTime() {
        if (popupShowing) return;

        clearAllTimers();

        warningTimer = setTimeout(showWarningPopup, jsTimeout - warningTime);
        idleTimer = setTimeout(forceLogout, jsTimeout);
    }

    function showWarningPopup() {
        if (popupShowing) return;

        popupShowing = true;

        let timeLeft = warningTime / 1000;

        Swal.fire({
            title: "Sắp hết phiên làm việc!",
            html: `Hệ thống sẽ tự động đăng xuất sau <b class="text-danger fs-5">${timeLeft}</b> giây nữa do không có hoạt động nào.<br><br>Bạn có muốn tiếp tục duy trì đăng nhập?`,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#8b5cf6",
            cancelButtonColor: "#6c757d",
            confirmButtonText: '<i class="bi bi-clock-history"></i> Duy trì đăng nhập',
            cancelButtonText: "Đăng xuất ngay",
            timer: warningTime,
            timerProgressBar: true,
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => {
                const b = Swal.getHtmlContainer().querySelector("b");

                timerInterval = setInterval(() => {
                    timeLeft -= 1;

                    if (b) {
                        b.textContent = Math.max(timeLeft, 0);
                    }
                }, 1000);
            },
            willClose: () => {
                clearInterval(timerInterval);
            }
        }).then(async function (result) {
            clearInterval(timerInterval);

            if (result.isConfirmed) {
                try {
                    await refreshSession();

                    popupShowing = false;
                    resetIdleTime();

                    Swal.fire({
                        title: "Đã gia hạn!",
                        text: "Phiên làm việc của bạn đã được làm mới.",
                        icon: "success",
                        timer: 1500,
                        showConfirmButton: false
                    });
                } catch (error) {
                    console.error("Lỗi gia hạn phiên:", error);
                    popupShowing = false;
                    forceLogout();
                }
            } else {
                popupShowing = false;
                forceLogout();
            }
        });
    }

    function forceLogout() {
        clearAllTimers();
        popupShowing = true;

        Swal.fire({
            title: "Đã hết phiên đăng nhập!",
            text: "Vui lòng đăng nhập lại để tiếp tục sử dụng hệ thống.",
            icon: "info",
            showConfirmButton: true,
            confirmButtonText: "Đăng nhập lại",
            confirmButtonColor: "#8b5cf6",
            allowOutsideClick: false,
            allowEscapeKey: false,
            timer: 5000,
            timerProgressBar: true
        }).then(() => {
            window.location.href = autoLogoutUrl || loginUrl;
        });
    }

    window.addEventListener("load", resetIdleTime);

    document.addEventListener("click", resetIdleTime);
    document.addEventListener("keydown", resetIdleTime);
    document.addEventListener("touchstart", resetIdleTime);
});