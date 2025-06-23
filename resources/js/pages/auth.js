$(document).ready(function () {
    // Toggle password visibility
    $("#togglePassword, #togglePasswordConfirm").on("click", function () {
        var target =
            $(this).attr("id") === "togglePassword"
                ? "#password"
                : "#password-confirm";
        var passwordField = $(target);
        var passwordFieldType = passwordField.attr("type");
        var icon = $(this).find("i");

        if (passwordFieldType === "password") {
            passwordField.attr("type", "text");
            icon.removeClass("fa-eye").addClass("fa-eye-slash");
        } else {
            passwordField.attr("type", "password");
            icon.removeClass("fa-eye-slash").addClass("fa-eye");
        }
    });

    // Password strength indicator (HANYA untuk register page)
    if ($("body").find('form[action*="register"]').length > 0) {
        $("#password").on("input", function () {
            var password = $(this).val();
            var strength = 0;

            if (password.length >= 8) strength++;
            if (/[a-z]/.test(password)) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^A-Za-z0-9]/.test(password)) strength++;

            var strengthText = [
                "Sangat Lemah",
                "Lemah",
                "Sedang",
                "Kuat",
                "Sangat Kuat",
            ];
            var strengthColor = [
                "danger",
                "warning",
                "info",
                "success",
                "success",
            ];

            // Remove existing strength indicator
            $(".password-strength").remove();

            if (password.length > 0) {
                $("#password")
                    .closest(".mb-3")
                    .append(
                        '<div class="password-strength mt-1">' +
                            '<small class="text-' +
                            strengthColor[strength - 1] +
                            '">Kekuatan: ' +
                            strengthText[strength - 1] +
                            "</small>" +
                            "</div>"
                    );
            }
        });
    }

    // Password confirmation match (HANYA untuk register page)
    if ($("body").find('form[action*="register"]').length > 0) {
        $("#password-confirm").on("input", function () {
            var password = $("#password").val();
            var confirmation = $(this).val();

            $(".password-match").remove();

            if (confirmation.length > 0) {
                var isMatch = password === confirmation;
                var message = isMatch
                    ? '<small class="text-success password-match"><i class="fas fa-check me-1"></i> Password cocok</small>'
                    : '<small class="text-danger password-match"><i class="fas fa-times me-1"></i> Password tidak cocok</small>';

                $(this)
                    .closest(".mb-3, .mb-4")
                    .append('<div class="mt-1">' + message + "</div>");
            }
        });
    }

    // Login form specific enhancements
    if ($("body").find('form[action*="login"]').length > 0) {
        console.log("Login page loaded");

        // Auto focus on email field
        $("#email").focus();

        // Remember me tooltip
        $("#remember").attr(
            "title",
            "Kami akan mengingat login Anda selama 30 hari"
        );
    }

    // Register form specific enhancements
    if ($("body").find('form[action*="register"]').length > 0) {
        console.log("Register page loaded");

        // Terms checkbox validation
        $("#terms").on("change", function () {
            var submitBtn = $('button[type="submit"]');
            if ($(this).is(":checked")) {
                submitBtn.prop("disabled", false);
            } else {
                submitBtn.prop("disabled", true);
            }
        });

        // Initially disable submit button
        $('button[type="submit"]').prop("disabled", true);
    }
});
