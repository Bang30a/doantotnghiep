document.addEventListener("DOMContentLoaded", function () {
    const refreshCsrfUrl = window.refreshCsrfUrl || "/refresh-csrf";
    let csrfRefreshPromise = null;

    async function refreshCsrfToken() {
        if (csrfRefreshPromise) {
            return csrfRefreshPromise;
        }

        const controller = new AbortController();
        const timeoutId = setTimeout(function () {
            controller.abort();
        }, 2500);

        csrfRefreshPromise = (async function () {
        try {
            const response = await fetch(refreshCsrfUrl, {
                method: "GET",
                credentials: "same-origin",
                cache: "no-store",
                signal: controller.signal,
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
            if (error.name !== "AbortError") {
                console.error("Không thể refresh CSRF token:", error);
            }
            return false;
        } finally {
            clearTimeout(timeoutId);
            csrfRefreshPromise = null;
        }
        })();

        return csrfRefreshPromise;
    }

    // Refresh token định kỳ trong nền, không chặn thao tác đăng nhập/đăng ký.
    setInterval(refreshCsrfToken, 2 * 60 * 1000);

    function getSubmitText(form) {
        const action = (form.getAttribute("action") || "").toLowerCase();

        if (action.includes("register")) {
            return "Đang đăng ký...";
        }

        if (action.includes("forgot-password")) {
            return "Đang gửi...";
        }

        if (action.includes("reset-password")) {
            return "Đang cập nhật...";
        }

        return "Đang đăng nhập...";
    }

    // Submit ngay để tránh cảm giác nút bị trễ khi refresh CSRF chậm.
    document.querySelectorAll("form").forEach(function (form) {
        form.addEventListener("submit", function () {
            if (form.dataset.submitting === "1") return;

            form.dataset.submitting = "1";

            form.querySelectorAll('button[type="submit"]').forEach(function (button) {
                button.disabled = true;
                button.innerHTML = '<span class="spinner-border spinner-border-sm"></span> ' + getSubmitText(form);
            });
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

    // Chuyển login/register nếu có nút switch.
    document.querySelectorAll(".switch-form-btn").forEach(function (btn) {
        btn.addEventListener("click", function () {
            refreshCsrfToken();
        });
    });
});
