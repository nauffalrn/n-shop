// Product Index Page Scripts
$(document).ready(function () {
    console.log("Product index page loaded");

    // Product card click untuk navigation
    $(".product-clickable").on("click", function (e) {
        // Pastikan bukan dari button atau form yang diklik
        if (!$(e.target).closest("button, form").length) {
            const productUrl = $(this).data("product-url");
            if (productUrl) {
                window.location.href = productUrl;
            }
        }
    });

    // Wishlist functionality untuk user saja
    $(".wishlist-btn").on("click", function (e) {
        e.preventDefault();
        e.stopPropagation();

        const productId = $(this).data("product-id");
        const btn = $(this);

        // Show loading state
        const originalIcon = btn.find("i").attr("class");
        btn.find("i").attr("class", "fas fa-spinner fa-spin");
        btn.prop("disabled", true);

        $.ajax({
            url: "/wishlist/" + productId + "/toggle",
            type: "POST",
            data: {
                _token: $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (response) {
                if (response.status === "added") {
                    btn.find("i").attr("class", "fas fa-heart text-danger");
                    btn.addClass("active");
                    showNotification(
                        "Produk ditambahkan ke wishlist!",
                        "success"
                    );
                } else {
                    btn.find("i").attr("class", "fas fa-heart text-muted");
                    btn.removeClass("active");
                    showNotification("Produk dihapus dari wishlist!", "info");
                }
            },
            error: function (xhr) {
                btn.find("i").attr("class", originalIcon);

                if (xhr.status === 401) {
                    showNotification(
                        "Silakan login terlebih dahulu!",
                        "warning"
                    );
                    setTimeout(() => {
                        window.location.href = "/login";
                    }, 1500);
                } else {
                    showNotification(
                        "Terjadi kesalahan. Silakan coba lagi.",
                        "error"
                    );
                }
            },
            complete: function () {
                btn.prop("disabled", false);
            },
        });
    });

    // Add to cart with loading state
    $(".product-card form").on("submit", function (e) {
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();

        // Show loading state
        submitBtn.prop("disabled", true);
        submitBtn.html(
            '<span class="spinner-border spinner-border-sm me-2"></span>Menambahkan...'
        );

        // Reset button after delay (form will be submitted)
        setTimeout(() => {
            submitBtn.prop("disabled", false);
            submitBtn.html(originalText);
        }, 2000);
    });

    // Enhanced hover effects
    $(".product-card").hover(
        function () {
            $(this).find(".product-image").addClass("scale-effect");
        },
        function () {
            $(this).find(".product-image").removeClass("scale-effect");
        }
    );

    // Smooth scrolling for pagination
    $(".pagination a").on("click", function (e) {
        if ($(this).attr("href") && $(this).attr("href") !== "#") {
            $("html, body").animate(
                {
                    scrollTop: $("#productsGrid").offset().top - 120,
                },
                600
            );
        }
    });

    // Filter form enhancements
    $("#filterForm select, #filterForm input").on("change", function () {
        const form = $("#filterForm");
        const submitBtn = form.find('button[type="submit"]');

        // Show loading on filter button
        submitBtn.prop("disabled", true);
        submitBtn.html(
            '<span class="spinner-border spinner-border-sm me-2"></span>Memfilter...'
        );

        // Auto submit after short delay
        setTimeout(() => {
            form.submit();
        }, 500);
    });
});

// Utility function for notifications
function showNotification(message, type = "info") {
    // Remove existing notifications
    $(".custom-notification").fadeOut().remove();

    const alertClass = {
        success: "alert-success",
        error: "alert-danger",
        warning: "alert-warning",
        info: "alert-info",
    };

    const notification = $(`
        <div class="alert ${
            alertClass[type]
        } alert-dismissible fade show custom-notification position-fixed" 
             style="top: 20px; right: 20px; z-index: 9999; min-width: 300px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
            <i class="fas fa-${
                type === "success"
                    ? "check-circle"
                    : type === "error"
                    ? "exclamation-triangle"
                    : type === "warning"
                    ? "exclamation-circle"
                    : "info-circle"
            } me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `);

    $("body").append(notification);

    // Auto remove after 4 seconds
    setTimeout(() => {
        notification.fadeOut(() => {
            notification.remove();
        });
    }, 4000);
}
