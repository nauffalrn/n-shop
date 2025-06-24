import "../css/app.css";

// Import Components
import "./components/product-detail";
import "./components/product-detail.js";
import "./components/drag-drop-upload";
import "./components/edit-product";
import "./components/wishlist";

// Import Page Scripts
import "./pages/auth";
import "./pages/cart";
import "./pages/order";
import "./pages/profile";
import "./pages/product-index";
import "./pages/product-detail-page.js";

// Global App Object
window.NShopApp = {
    init: function () {
        this.setupCSRFToken();
        this.initEventListeners();
        this.wishlist.init(); // Initialize wishlist
        console.log("N-Shop App initialized successfully!");
    },

    setupCSRFToken: function () {
        if (typeof $ !== "undefined") {
            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
            });
        }
    },

    initEventListeners: function () {
        // Auto-dismiss alerts after 5 seconds
        setTimeout(function () {
            $(".alert").fadeOut("slow");
        }, 5000);

        // Global form loading states (excluding forms with no-loading class)
        $("form:not(.no-loading)").on("submit", function () {
            var submitBtn = $(this).find('button[type="submit"]');
            if (!submitBtn.hasClass("no-loading")) {
                setTimeout(() => {
                    NShopApp.utils.showLoading(submitBtn);
                }, 100);
            }
        });
    },

    utils: {
        formatCurrency: function (amount) {
            return "Rp " + new Intl.NumberFormat("id-ID").format(amount);
        },

        showLoading: function (element) {
            if (typeof $ !== "undefined") {
                var originalText = $(element).html();
                $(element)
                    .data("original-text", originalText)
                    .prop("disabled", true)
                    .html(
                        '<span class="spinner-border spinner-border-sm me-2"></span>Loading...'
                    );
            }
        },

        hideLoading: function (element, originalText) {
            if (typeof $ !== "undefined") {
                var text =
                    originalText ||
                    $(element).data("original-text") ||
                    "Submit";
                $(element).prop("disabled", false).html(text);
            }
        },

        showNotification: function (message, type = "info") {
            // Utility notification function (shared across pages)
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

            setTimeout(() => {
                notification.fadeOut(() => notification.remove());
            }, 4000);
        },
    },

    wishlist: {
        init: function () {
            this.bindWishlistEvents();
        },

        bindWishlistEvents: function () {
            // Handle wishlist toggle di semua halaman
            $(document).on("click", ".wishlist-btn", function (e) {
                e.preventDefault();
                e.stopPropagation();

                const btn = $(this);
                const productId = btn.data("product-id");
                const icon = btn.find("i");
                const originalClass = icon.attr("class");

                // Disable button dan tampilkan loading
                btn.prop("disabled", true);
                icon.attr("class", "fas fa-spinner fa-spin");

                // Kirim AJAX request
                $.ajax({
                    url: "/wishlist/" + productId + "/toggle",
                    type: "POST",
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
                    },
                    dataType: "json",
                    success: function (response) {
                        // Update semua tombol wishlist untuk produk yang sama
                        NShopApp.wishlist.updateAllWishlistButtons(
                            productId,
                            response.status
                        );

                        // Tampilkan notifikasi
                        if (response.status === "added") {
                            NShopApp.utils.showNotification(
                                "Produk ditambahkan ke wishlist!",
                                "success"
                            );
                        } else {
                            NShopApp.utils.showNotification(
                                "Produk dihapus dari wishlist!",
                                "info"
                            );
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error("Wishlist error:", error);
                        icon.attr("class", originalClass);
                        NShopApp.utils.showNotification(
                            "Terjadi kesalahan. Silakan coba lagi.",
                            "error"
                        );
                    },
                    complete: function () {
                        btn.prop("disabled", false);
                    },
                });
            });
        },

        updateAllWishlistButtons: function (productId, status) {
            // Update semua tombol wishlist untuk produk yang sama di halaman
            $('.wishlist-btn[data-product-id="' + productId + '"]').each(
                function () {
                    const btn = $(this);
                    const icon = btn.find("i");

                    if (status === "added") {
                        icon.attr("class", "fas fa-heart text-danger");
                        btn.addClass("active");
                    } else {
                        icon.attr("class", "fas fa-heart text-muted");
                        btn.removeClass("active");
                    }
                }
            );
        },
    },
};

// Initialize app when document is ready
$(document).ready(function () {
    NShopApp.init();
});
