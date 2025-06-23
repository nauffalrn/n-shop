class ProductDetail {
    constructor() {
        this.currentImageIndex = 0;
        this.selectedVariant = null;
        this.init();
    }

    init() {
        this.bindEvents();
        this.initImageGallery();
        this.initVariantSelection();
        this.initAddToCart();
    }

    bindEvents() {
        // Global functions for blade templates
        window.changeMainImage = (imageUrl, index) =>
            this.changeMainImage(imageUrl, index);
    }

    initImageGallery() {
        // Initialize thumbnail navigation
        const thumbnails = document.querySelectorAll(".thumbnail-item");

        thumbnails.forEach((thumb, index) => {
            thumb.addEventListener("click", () => {
                const img = thumb.querySelector("img");
                if (img) {
                    this.changeMainImage(img.src, index);
                }
            });
        });

        // Initialize keyboard navigation
        document.addEventListener("keydown", (e) => {
            if (e.key === "ArrowLeft") {
                this.prevImage();
            } else if (e.key === "ArrowRight") {
                this.nextImage();
            }
        });

        // Initialize touch/swipe gestures
        this.initTouchGestures();
    }

    changeMainImage(imageUrl, index) {
        const mainImage = document.getElementById("mainImage");
        if (!mainImage) return;

        // Update main image with fade effect
        mainImage.style.opacity = "0.7";

        setTimeout(() => {
            mainImage.src = imageUrl;
            mainImage.style.opacity = "1";
        }, 150);

        // Update active thumbnail
        document.querySelectorAll(".thumbnail-item").forEach((item) => {
            item.classList.remove("active");
        });

        const thumbnails = document.querySelectorAll(".thumbnail-item");
        if (thumbnails[index]) {
            thumbnails[index].classList.add("active");
        }

        this.currentImageIndex = index;
    }

    prevImage() {
        const thumbnails = document.querySelectorAll(".thumbnail-item");
        if (thumbnails.length === 0) return;

        const prevIndex =
            this.currentImageIndex > 0
                ? this.currentImageIndex - 1
                : thumbnails.length - 1;

        const prevThumb = thumbnails[prevIndex];
        const img = prevThumb.querySelector("img");
        if (img) {
            this.changeMainImage(img.src, prevIndex);
        }
    }

    nextImage() {
        const thumbnails = document.querySelectorAll(".thumbnail-item");
        if (thumbnails.length === 0) return;

        const nextIndex =
            this.currentImageIndex < thumbnails.length - 1
                ? this.currentImageIndex + 1
                : 0;

        const nextThumb = thumbnails[nextIndex];
        const img = nextThumb.querySelector("img");
        if (img) {
            this.changeMainImage(img.src, nextIndex);
        }
    }

    initTouchGestures() {
        const mainImageContainer =
            document.getElementById("mainImageContainer");
        if (!mainImageContainer) return;

        let startX = 0;
        let startY = 0;

        mainImageContainer.addEventListener("touchstart", (e) => {
            startX = e.touches[0].clientX;
            startY = e.touches[0].clientY;
        });

        mainImageContainer.addEventListener("touchend", (e) => {
            if (!startX || !startY) return;

            const endX = e.changedTouches[0].clientX;
            const endY = e.changedTouches[0].clientY;

            const diffX = startX - endX;
            const diffY = startY - endY;

            // Only process horizontal swipes
            if (Math.abs(diffX) > Math.abs(diffY)) {
                if (Math.abs(diffX) > 50) {
                    // Minimum swipe distance
                    if (diffX > 0) {
                        this.nextImage();
                    } else {
                        this.prevImage();
                    }
                }
            }

            startX = 0;
            startY = 0;
        });
    }

    initVariantSelection() {
        const variantInputs = document.querySelectorAll(
            'input[name="variant"]'
        );
        const selectedVariantInput = document.getElementById("selectedVariant");

        variantInputs.forEach((input) => {
            input.addEventListener("change", () => {
                this.selectedVariant = input.value;
                if (selectedVariantInput) {
                    selectedVariantInput.value = input.value;
                }

                // Update UI based on variant selection
                this.updateVariantUI(input);
            });
        });
    }

    updateVariantUI(selectedInput) {
        // Remove active class from all variant options
        document.querySelectorAll(".form-check").forEach((check) => {
            check.classList.remove("variant-selected");
        });

        // Add active class to selected variant
        selectedInput.closest(".form-check").classList.add("variant-selected");

        // You can add price updates here if variants have different prices
        // this.updatePrice(selectedInput.dataset.price);
    }

    initAddToCart() {
        const addToCartForm = document.querySelector(
            'form[action*="add_to_cart"]'
        );
        if (!addToCartForm) return;

        const quantityInput = document.getElementById("amount");
        const addToCartBtn = addToCartForm.querySelector(
            'button[type="submit"]'
        );

        // Quantity input validation
        if (quantityInput) {
            quantityInput.addEventListener("input", () => {
                this.validateQuantity(quantityInput);
            });

            // Add quantity controls
            this.addQuantityControls(quantityInput);
        }

        // Add to cart form submission
        addToCartForm.addEventListener("submit", (e) => {
            if (!this.validateAddToCart(quantityInput)) {
                e.preventDefault();
            } else {
                this.handleAddToCart(addToCartBtn);
            }
        });
    }

    addQuantityControls(quantityInput) {
        const inputGroup = quantityInput.closest(".col-4");
        if (!inputGroup) return;

        const decreaseBtn = document.createElement("button");
        decreaseBtn.type = "button";
        decreaseBtn.className = "btn btn-outline-secondary";
        decreaseBtn.innerHTML = '<i class="fas fa-minus"></i>';
        decreaseBtn.addEventListener("click", () => {
            const currentValue = parseInt(quantityInput.value) || 1;
            if (currentValue > 1) {
                quantityInput.value = currentValue - 1;
                this.validateQuantity(quantityInput);
            }
        });

        const increaseBtn = document.createElement("button");
        increaseBtn.type = "button";
        increaseBtn.className = "btn btn-outline-secondary";
        increaseBtn.innerHTML = '<i class="fas fa-plus"></i>';
        increaseBtn.addEventListener("click", () => {
            const currentValue = parseInt(quantityInput.value) || 1;
            const maxValue = parseInt(quantityInput.max) || 999;
            if (currentValue < maxValue) {
                quantityInput.value = currentValue + 1;
                this.validateQuantity(quantityInput);
            }
        });

        // Wrap input in input group
        const wrapper = document.createElement("div");
        wrapper.className = "input-group";

        quantityInput.parentNode.insertBefore(wrapper, quantityInput);
        wrapper.appendChild(decreaseBtn);
        wrapper.appendChild(quantityInput);
        wrapper.appendChild(increaseBtn);
    }

    validateQuantity(input) {
        const value = parseInt(input.value);
        const min = parseInt(input.min) || 1;
        const max = parseInt(input.max) || 999;

        if (isNaN(value) || value < min) {
            input.value = min;
            return false;
        }

        if (value > max) {
            input.value = max;
            this.showNotification(`Maksimal ${max} item`, "warning");
            return false;
        }

        return true;
    }

    validateAddToCart(quantityInput) {
        // Validate quantity
        if (!this.validateQuantity(quantityInput)) {
            return false;
        }

        // Check if variant is required but not selected
        const variantInputs = document.querySelectorAll(
            'input[name="variant"]'
        );
        if (variantInputs.length > 0 && !this.selectedVariant) {
            this.showNotification("Silakan pilih varian produk", "warning");
            return false;
        }

        return true;
    }

    handleAddToCart(button) {
        const originalText = button.innerHTML;

        // Show loading state
        button.disabled = true;
        button.innerHTML =
            '<span class="spinner-border spinner-border-sm me-2"></span>Menambahkan...';

        // Reset button after delay (form submission will handle the actual process)
        setTimeout(() => {
            button.disabled = false;
            button.innerHTML = originalText;
        }, 2000);
    }

    showNotification(message, type = "info") {
        // Create notification element
        const notification = document.createElement("div");
        notification.className = `alert alert-${
            type === "error" ? "danger" : type
        } alert-dismissible fade show position-fixed`;
        notification.style.cssText = `
            top: 20px;
            right: 20px;
            z-index: 9999;
            min-width: 300px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        `;

        notification.innerHTML = `
            <i class="fas fa-${
                type === "success"
                    ? "check-circle"
                    : type === "warning"
                    ? "exclamation-triangle"
                    : "info-circle"
            } me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        document.body.appendChild(notification);

        // Auto remove after 3 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 3000);
    }
}

// Initialize when DOM is ready
document.addEventListener("DOMContentLoaded", function () {
    // Only initialize on product detail pages
    if (document.querySelector(".product-gallery")) {
        window.productDetail = new ProductDetail();
    }
});

// Expose for external use
window.ProductDetail = ProductDetail;

window.ProductDetailComponent = {
    init: function () {
        this.bindEvents();
        this.initImageGallery();
        this.initVariantSelection();
    },

    bindEvents: function () {
        // Thumbnail navigation
        $(".thumbnail-nav").on("click", this.handleThumbnailClick);

        // Variant selection
        $(".variant-option").on("click", this.handleVariantSelection);

        // Quantity controls
        $(".qty-btn").on("click", this.handleQuantityChange);
    },

    handleThumbnailClick: function () {
        $(".thumbnail-nav").removeClass("active-thumb");
        $(this).addClass("active-thumb");

        const newImageSrc = $(this).attr("src");
        $(".product-main-image").attr("src", newImageSrc);
    },

    handleVariantSelection: function () {
        $(".variant-option").removeClass("selected");
        $(this).addClass("selected");

        const variantId = $(this).data("variant-id");
        const price = $(this).data("price");
        const stock = $(this).data("stock");

        $("#selectedVariantId").val(variantId);
        $("#amount").attr("max", stock);
        $(".product-price h2").text("Rp " + price.toLocaleString("id-ID"));

        // Update stock info
        $(".stock-info").text(stock + " tersedia");
    },

    handleQuantityChange: function () {
        const action = $(this).data("action");
        const input = $(this).closest(".input-group").find(".qty-input");
        const currentVal = parseInt(input.val());
        const max = parseInt(input.attr("max"));
        const min = parseInt(input.attr("min")) || 1;

        if (action === "increase" && currentVal < max) {
            input.val(currentVal + 1);
        } else if (action === "decrease" && currentVal > min) {
            input.val(currentVal - 1);
        }
    },

    initImageGallery: function () {
        // Initialize image gallery if needed
        if ($(".product-gallery").length > 0) {
            console.log("Product gallery initialized");
        }
    },

    initVariantSelection: function () {
        // Auto-select first variant if only one exists
        if ($(".variant-option").length === 1) {
            $(".variant-option").first().click();
        }
    },
};

// Initialize when document ready
$(document).ready(function () {
    if (
        $(".product-detail-page").length > 0 ||
        $(".product-main-image").length > 0
    ) {
        ProductDetailComponent.init();
    }
});
