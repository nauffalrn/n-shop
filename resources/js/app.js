import "../css/app.css";

// Import Components
import "./components/product-detail";
import "./components/drag-drop-upload";
import "./components/edit-product";
import "./components/wishlist";

// Import Page Scripts
import "./pages/auth";
import "./pages/cart";
import "./pages/order";
import "./pages/profile";

// Global App Object
window.NShopApp = {
    init: function () {
        this.setupCSRFToken();
        this.initEventListeners();
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

        // Global form loading states
        $("form").on("submit", function () {
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
    },
};

// Initialize app when document is ready
$(document).ready(function () {
    NShopApp.init();
});
