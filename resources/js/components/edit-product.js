class EditProductImageManager {
    constructor() {
        this.newFiles = [];
        this.existingImages = this.getExistingImages();
        this.newSortableInstance = null;
        this.existingSortableInstance = null;
        this.maxFiles = 5;
        this.maxFileSize = 2 * 1024 * 1024; // 2MB
        this.acceptedTypes = [
            "image/jpeg",
            "image/jpg",
            "image/png",
            "image/gif",
        ];

        this.init();
    }

    init() {
        this.bindEvents();
        this.initExistingSortable();
        this.initDragDropForNew();
    }

    getExistingImages() {
        const existingContainer = document.getElementById("existingImages");
        if (!existingContainer) return [];

        const items = existingContainer.querySelectorAll(
            '.existing-image-item input[type="hidden"]'
        );
        return Array.from(items).map((input) => input.value);
    }

    bindEvents() {
        const dropZone = document.getElementById("dropZone");
        const imageInput = document.getElementById("imageInput");

        if (dropZone && imageInput) {
            // Drag & Drop Events
            dropZone.addEventListener("dragover", (e) =>
                this.handleDragOver(e)
            );
            dropZone.addEventListener("dragleave", (e) =>
                this.handleDragLeave(e)
            );
            dropZone.addEventListener("drop", (e) => this.handleDrop(e));

            // File Input Change
            imageInput.addEventListener("change", (e) =>
                this.handleFileSelect(e)
            );
        }

        // Global functions for blade templates
        window.removeExistingImage = (index) => this.removeExistingImage(index);
        window.removeNewImage = (index) => this.removeNewImage(index);
    }

    initExistingSortable() {
        const existingContainer = document.getElementById("existingImages");
        if (!existingContainer) return;

        this.existingSortableInstance = new Sortable(existingContainer, {
            animation: 200,
            ghostClass: "sortable-ghost",
            dragClass: "sortable-drag",
            chosenClass: "sortable-chosen",
            onStart: () => {
                existingContainer.classList.add("sortable-drag");
            },
            onEnd: () => {
                existingContainer.classList.remove("sortable-drag");
                this.updateExistingOrder();
            },
        });
    }

    initDragDropForNew() {
        // This will be initialized when new images are added
        // Similar to the drag-drop component we created earlier
    }

    handleDragOver(e) {
        e.preventDefault();
        const dropZone = document.getElementById("dropZone");
        dropZone.classList.add("drag-over");
    }

    handleDragLeave(e) {
        e.preventDefault();
        const dropZone = document.getElementById("dropZone");
        if (!dropZone.contains(e.relatedTarget)) {
            dropZone.classList.remove("drag-over");
        }
    }

    handleDrop(e) {
        e.preventDefault();
        const dropZone = document.getElementById("dropZone");
        dropZone.classList.remove("drag-over");

        const files = Array.from(e.dataTransfer.files);
        this.handleFiles(files);
    }

    handleFileSelect(e) {
        const files = Array.from(e.target.files);
        this.handleFiles(files);
    }

    handleFiles(files) {
        const validFiles = files.filter((file) => this.validateFile(file));

        if (validFiles.length === 0) {
            this.showNotification(
                "Tidak ada file valid yang dipilih!",
                "error"
            );
            return;
        }

        // Check total limit
        const totalImages =
            this.existingImages.length +
            this.newFiles.length +
            validFiles.length;
        if (totalImages > this.maxFiles) {
            this.showNotification(
                `Maksimal ${this.maxFiles} gambar total!`,
                "warning"
            );
            return;
        }

        validFiles.forEach((file) => {
            this.newFiles.push(file);
        });

        this.updateNewPreview();
        this.updateFileInput();
        this.showNotification(
            `${validFiles.length} gambar baru ditambahkan!`,
            "success"
        );
    }

    validateFile(file) {
        if (!this.acceptedTypes.includes(file.type)) {
            this.showNotification(
                `Format ${file.name} tidak didukung!`,
                "error"
            );
            return false;
        }

        if (file.size > this.maxFileSize) {
            const sizeMB = (this.maxFileSize / (1024 * 1024)).toFixed(1);
            this.showNotification(
                `${file.name} terlalu besar! Maksimal ${sizeMB}MB`,
                "error"
            );
            return false;
        }

        return true;
    }

    updateNewPreview() {
        const preview = document.getElementById("newImagePreview");
        const container = document.getElementById("sortableNewImages");

        if (!preview || !container) return;

        if (this.newFiles.length === 0) {
            preview.style.display = "none";
            return;
        }

        preview.style.display = "block";
        container.innerHTML = "";

        this.newFiles.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = (e) => {
                const imageDiv = document.createElement("div");
                imageDiv.className = "image-preview-item";
                imageDiv.style.width = "120px";
                imageDiv.style.height = "120px";
                imageDiv.dataset.index = index;

                imageDiv.innerHTML = `
                    <img src="${
                        e.target.result
                    }" class="image-preview-img" alt="New ${index + 1}">
                    <div class="drag-handle">
                        <i class="fas fa-arrows-alt"></i>
                    </div>
                    <div class="order-indicator">${
                        this.existingImages.length + index + 1
                    }</div>
                    <button type="button" class="remove-image" onclick="removeNewImage(${index})" title="Hapus gambar">
                        <i class="fas fa-times"></i>
                    </button>
                `;

                container.appendChild(imageDiv);
            };
            reader.readAsDataURL(file);
        });

        setTimeout(() => {
            this.initNewSortable();
        }, 100);
    }

    initNewSortable() {
        const newContainer = document.getElementById("sortableNewImages");
        if (!newContainer) return;

        if (this.newSortableInstance) {
            this.newSortableInstance.destroy();
        }

        this.newSortableInstance = new Sortable(newContainer, {
            animation: 200,
            ghostClass: "sortable-ghost",
            dragClass: "sortable-drag",
            chosenClass: "sortable-chosen",
            onStart: () => {
                newContainer.classList.add("sortable-drag");
            },
            onEnd: () => {
                newContainer.classList.remove("sortable-drag");
                this.updateNewOrder();
            },
        });
    }

    removeExistingImage(index) {
        const container = document.getElementById("existingImages");
        const items = container.querySelectorAll(".existing-image-item");

        if (items[index]) {
            items[index].remove();
            this.existingImages.splice(index, 1);
            this.updateExistingDisplay();
            this.showNotification("Gambar existing dihapus!", "info");
        }
    }

    removeNewImage(index) {
        if (index >= 0 && index < this.newFiles.length) {
            const fileName = this.newFiles[index].name;
            this.newFiles.splice(index, 1);
            this.updateNewPreview();
            this.updateFileInput();
            this.showNotification(`${fileName} dihapus!`, "info");
        }
    }

    updateExistingOrder() {
        const container = document.getElementById("existingImages");
        const items = Array.from(
            container.querySelectorAll(".existing-image-item")
        );

        // Update order indicators
        items.forEach((item, index) => {
            const orderIndicator = item.querySelector(".order-indicator");
            if (orderIndicator) {
                orderIndicator.textContent = index + 1;
            }

            // Update main badge
            const mainBadge = item.querySelector(".main-badge");
            if (index === 0) {
                if (!mainBadge) {
                    const badge = document.createElement("div");
                    badge.className = "main-badge";
                    badge.textContent = "UTAMA";
                    item.querySelector(".position-relative").appendChild(badge);
                }
                item.style.borderColor = "var(--success-color)";
                item.style.borderWidth = "3px";
            } else {
                if (mainBadge) {
                    mainBadge.remove();
                }
                item.style.borderColor = "#dee2e6";
                item.style.borderWidth = "2px";
            }
        });

        this.showNotification("Urutan gambar berhasil diubah!", "info");
    }

    updateNewOrder() {
        const items = Array.from(
            document.querySelectorAll("#sortableNewImages .image-preview-item")
        );
        const newOrder = items.map((item) => parseInt(item.dataset.index));

        this.newFiles = newOrder.map((index) => this.newFiles[index]);
        this.updateNewPreview();
        this.updateFileInput();
    }

    updateFileInput() {
        const fileInput = document.getElementById("imageInput");
        if (!fileInput) return;

        const dt = new DataTransfer();
        this.newFiles.forEach((file) => {
            dt.items.add(file);
        });
        fileInput.files = dt.files;
    }

    updateExistingDisplay() {
        // This method updates the existing images display after reordering
        const container = document.getElementById("existingImages");
        const items = Array.from(
            container.querySelectorAll(".existing-image-item")
        );

        items.forEach((item, index) => {
            const orderIndicator = item.querySelector(".order-indicator");
            if (orderIndicator) {
                orderIndicator.textContent = index + 1;
            }

            // Update onclick handlers
            const removeBtn = item.querySelector(".remove-existing-image");
            if (removeBtn) {
                removeBtn.setAttribute(
                    "onclick",
                    `removeExistingImage(${index})`
                );
            }
        });
    }

    showNotification(message, type = "info") {
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
            border-radius: 8px;
        `;

        const icon =
            type === "success"
                ? "check-circle"
                : type === "warning"
                ? "exclamation-triangle"
                : type === "error"
                ? "times-circle"
                : "info-circle";

        notification.innerHTML = `
            <i class="fas fa-${icon} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        document.body.appendChild(notification);

        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 3000);
    }

    // Public methods
    getNewFiles() {
        return this.newFiles;
    }

    getExistingImagesOrder() {
        return this.existingImages;
    }

    clearNewFiles() {
        this.newFiles = [];
        this.updateNewPreview();
        this.updateFileInput();
    }
}

// Initialize when DOM is ready
document.addEventListener("DOMContentLoaded", function () {
    // Only initialize on edit product pages
    if (
        document.getElementById("existingImages") ||
        document.getElementById("dropZone")
    ) {
        window.editProductImageManager = new EditProductImageManager();
    }
});

// Expose for external use
window.EditProductImageManager = EditProductImageManager;
