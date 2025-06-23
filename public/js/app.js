// Global App Object
const NShopApp = {
    // Initialize app
    init: function () {
        this.setupCSRFToken();
        this.initEventListeners();
    },

    // Setup CSRF Token for AJAX requests
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

    // Initialize event listeners
    initEventListeners: function () {
        // Auto-dismiss alerts after 5 seconds
        setTimeout(function () {
            $(".alert").fadeOut("slow");
        }, 5000);
    },

    // Wishlist functions
    wishlist: {
        toggle: function (productId, callback) {
            if (typeof $ === "undefined") return;

            $.post(`/wishlist/${productId}/toggle`)
                .done(function (response) {
                    if (callback && typeof callback === "function") {
                        callback(response);
                    }
                })
                .fail(function () {
                    alert("Terjadi kesalahan. Silakan coba lagi.");
                });
        },
    },

    // Product functions
    product: {
        // Initialize product list functionality
        initProductList: function () {
            if (typeof $ === "undefined") return;

            // Wishlist toggle
            $(".wishlist-btn").on("click", function () {
                const productId = $(this).data("product-id");
                const btn = $(this);

                NShopApp.wishlist.toggle(productId, function (response) {
                    if (response.status === "added") {
                        btn.find("i")
                            .removeClass("text-muted")
                            .addClass("text-danger");
                    } else {
                        btn.find("i")
                            .removeClass("text-danger")
                            .addClass("text-muted");
                    }
                });
            });

            // View toggle (grid/list)
            $("#listView").on("click", function () {
                $("#productsContainer")
                    .removeClass("row")
                    .addClass("list-view");
                $("#gridView").removeClass("active");
                $(this).addClass("active");
            });

            $("#gridView").on("click", function () {
                $("#productsContainer")
                    .removeClass("list-view")
                    .addClass("row");
                $("#listView").removeClass("active");
                $(this).addClass("active");
            });
        },
    },

    // Cart functions
    cart: {
        // Update cart quantity
        updateQuantity: function (cartId, quantity) {
            if (typeof $ === "undefined") return;

            $.post(`/cart/${cartId}`, {
                _method: "PATCH",
                amount: quantity,
            })
                .done(function () {
                    location.reload();
                })
                .fail(function () {
                    alert("Gagal memperbarui keranjang");
                });
        },

        // Remove item from cart
        removeItem: function (cartId) {
            if (typeof $ === "undefined") return;

            if (confirm("Yakin ingin menghapus item ini dari keranjang?")) {
                $.post(`/cart/${cartId}`, {
                    _method: "DELETE",
                })
                    .done(function () {
                        location.reload();
                    })
                    .fail(function () {
                        alert("Gagal menghapus item");
                    });
            }
        },
    },

    // Utility functions
    utils: {
        // Format currency
        formatCurrency: function (amount) {
            return "Rp " + new Intl.NumberFormat("id-ID").format(amount);
        },

        // Show loading state
        showLoading: function (element) {
            if (typeof $ !== "undefined") {
                $(element)
                    .prop("disabled", true)
                    .html('<span class="loading"></span> Loading...');
            }
        },

        // Hide loading state
        hideLoading: function (element, originalText) {
            if (typeof $ !== "undefined") {
                $(element).prop("disabled", false).html(originalText);
            }
        },
    },
};

// Initialize app when document is ready
$(document).ready(function () {
    NShopApp.init();
});

// Fallback jika jQuery tidak ada
if (typeof $ === "undefined") {
    console.error("jQuery is not loaded!");
}
