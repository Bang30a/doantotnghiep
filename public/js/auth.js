document.addEventListener("DOMContentLoaded", function () {
    const refreshCsrfUrl = window.refreshCsrfUrl || "/refresh-csrf";

    async function refreshCsrfToken() {
        try {
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
                return false;
            }

            const data = await response.json();

            if (!data.csrf_token) {
                return false;
            }

            const meta = document.querySelector('meta[name="csrf-token"]');
            if (meta) {
                meta.setAttribute("content", data.csrf_token);
            }

            document.querySelectorAll('input[name="_token"]').forEach(function (input) {
                input.value = data.csrf_token;
            });

            return true;
        } catch (error) {
            console.error("Khong the refresh CSRF token:", error);
            return false;
        }
    }

    // Refresh token ngay khi mo trang login/register
    refreshCsrfToken();

    // Cu 1 phut refresh token 1 lan khi dang o trang login/register
    setInterval(refreshCsrfToken, 60 * 1000);

    // Truoc khi submit form login/register, refresh token lan cuoi
    document.querySelectorAll("form").forEach(function (form) {
        form.addEventListener("submit", async function (e) {
            if (form.dataset.csrfRefreshed === "1") {
                return true;
            }

            e.preventDefault();

            const ok = await refreshCsrfToken();

            if (ok) {
                form.dataset.csrfRefreshed = "1";
                form.submit();
            } else {
                window.location.reload();
            }
        });
    });

    // Toggle password
    document.querySelectorAll(".toggle-password").forEach(function (icon) {
        icon.addEventListener("click", function () {
            const inputGroup = icon.closest(".input-group");
            if (!inputGroup) return;

            const input = inputGroup.querySelector("input");
            if (!input) return;

            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove("bi-eye");
                icon.classList.add("bi-eye-slash");
            } else {
                input.type = "password";
                icon.classList.remove("bi-eye-slash");
                icon.classList.add("bi-eye");
            }
        });
    });

    // Chuyen login/register neu co nut switch
    document.querySelectorAll(".switch-form-btn").forEach(function (btn) {
        btn.addEventListener("click", function () {
            refreshCsrfToken();
        });
    });
});