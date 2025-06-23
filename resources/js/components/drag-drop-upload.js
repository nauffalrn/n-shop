class DragDropUpload {
    constructor(options = {}) {
        this.options = {
            maxFiles: 10,
            maxFileSize: 2 * 1024 * 1024, // 2MB
            acceptedTypes: ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'],
            dropZoneId: 'dropZone',
            fileInputId: 'imageInput',
            previewId: 'imagePreview',
            sortableId: 'sortableImages',
            ...options
        };

        this.selectedFiles = [];
        this.sortableInstance = null;
        
        this.init();
    }

    init() {
        this.bindElements();
        this.bindEvents();
    }

    bindElements() {
        this.dropZone = document.getElementById(this.options.dropZoneId);
        this.fileInput = document.getElementById(this.options.fileInputId);
        this.preview = document.getElementById(this.options.previewId);
        this.sortableContainer = document.getElementById(this.options.sortableId);

        if (!this.dropZone || !this.fileInput || !this.preview || !this.sortableContainer) {
            console.error('DragDropUpload: Required elements not found');
            return;
        }
    }

    bindEvents() {
        // Drag & Drop Events
        this.dropZone.addEventListener('dragover', (e) => this.handleDragOver(e));
        this.dropZone.addEventListener('dragleave', (e) => this.handleDragLeave(e));
        this.dropZone.addEventListener('drop', (e) => this.handleDrop(e));
        
        // File Input Change
        this.fileInput.addEventListener('change', (e) => this.handleFileSelect(e));
        
        // Global function for removing images
        window.removeImage = (index) => this.removeImage(index);
    }

    handleDragOver(e) {
        e.preventDefault();
        this.dropZone.classList.add('drag-over');
    }

    handleDragLeave(e) {
        e.preventDefault();
        // Only remove class if we're actually leaving the drop zone
        if (!this.dropZone.contains(e.relatedTarget)) {
            this.dropZone.classList.remove('drag-over');
        }
    }

    handleDrop(e) {
        e.preventDefault();
        this.dropZone.classList.remove('drag-over');
        
        const files = Array.from(e.dataTransfer.files);
        this.handleFiles(files);
    }

    handleFileSelect(e) {
        const files = Array.from(e.target.files);
        this.handleFiles(files);
    }

    handleFiles(files) {
        // Filter and validate files
        const validFiles = files.filter(file => this.validateFile(file));
        
        if (validFiles.length === 0) {
            this.showNotification('Tidak ada file valid yang dipilih!', 'error');
            return;
        }

        // Check total file limit
        const totalFiles = this.selectedFiles.length + validFiles.length;
        if (totalFiles > this.options.maxFiles) {
            this.showNotification(`Maksimal ${this.options.maxFiles} gambar diperbolehkan!`, 'warning');
            return;
        }

        // Add files to collection
        validFiles.forEach(file => {
            if (this.selectedFiles.length < this.options.maxFiles) {
                this.selectedFiles.push(file);
            }
        });

        this.updatePreview();
        this.updateFileInput();
        this.showNotification(`${validFiles.length} gambar berhasil ditambahkan!`, 'success');
    }

    validateFile(file) {
        // Check file type
        if (!this.options.acceptedTypes.includes(file.type)) {
            this.showNotification(`Format ${file.name} tidak didukung!`, 'error');
            return false;
        }

        // Check file size
        if (file.size > this.options.maxFileSize) {
            const sizeMB = (this.options.maxFileSize / (1024 * 1024)).toFixed(1);
            this.showNotification(`${file.name} terlalu besar! Maksimal ${sizeMB}MB`, 'error');
            return false;
        }

        return true;
    }

    updatePreview() {
        if (this.selectedFiles.length === 0) {
            this.preview.style.display = 'none';
            return;
        }

        this.preview.style.display = 'block';
        this.sortableContainer.innerHTML = '';

        // Create preview for each file
        this.selectedFiles.forEach((file, index) => {
            this.createPreviewItem(file, index);
        });

        // Initialize sortable after all images are loaded
        setTimeout(() => {
            this.initSortable();
        }, 100);
    }

    createPreviewItem(file, index) {
        const reader = new FileReader();
        
        reader.onload = (e) => {
            const imageDiv = document.createElement('div');
            imageDiv.className = 'image-preview-item';
            imageDiv.style.width = '120px';
            imageDiv.style.height = '120px';
            imageDiv.dataset.index = index;
            
            if (index === 0) {
                imageDiv.classList.add('main-image');
            }

            imageDiv.innerHTML = `
                <img src="${e.target.result}" class="image-preview-img" alt="Preview ${index + 1}">
                <div class="drag-handle">
                    <i class="fas fa-arrows-alt"></i>
                </div>
                ${index === 0 ? '<div class="main-badge">UTAMA</div>' : ''}
                <div class="order-indicator">${index + 1}</div>
                <button type="button" class="remove-image" onclick="removeImage(${index})" title="Hapus gambar">
                    <i class="fas fa-times"></i>
                </button>
            `;

            this.sortableContainer.appendChild(imageDiv);
        };
        
        reader.readAsDataURL(file);
    }

    initSortable() {
        if (this.sortableInstance) {
            this.sortableInstance.destroy();
        }

        this.sortableInstance = new Sortable(this.sortableContainer, {
            animation: 200,
            ghostClass: 'sortable-ghost',
            dragClass: 'sortable-drag',
            chosenClass: 'sortable-chosen',
            onStart: () => {
                this.sortableContainer.classList.add('sortable-drag');
            },
            onEnd: (evt) => {
                this.sortableContainer.classList.remove('sortable-drag');
                this.reorderFiles(evt);
            }
        });
    }

    reorderFiles(evt) {
        // Get new order based on DOM elements
        const newOrder = Array.from(this.sortableContainer.children).map(child => 
            parseInt(child.dataset.index)
        );
        
        // Reorder files array
        const reorderedFiles = newOrder.map(index => this.selectedFiles[index]);
        this.selectedFiles = reorderedFiles;
        
        // Update preview and file input
        this.updatePreview();
        this.updateFileInput();
        
        this.showNotification('Urutan gambar berhasil diubah!', 'info');
    }

    removeImage(index) {
        if (index >= 0 && index < this.selectedFiles.length) {
            const fileName = this.selectedFiles[index].name;
            this.selectedFiles.splice(index, 1);
            this.updatePreview();
            this.updateFileInput();
            this.showNotification(`${fileName} dihapus!`, 'info');
        }
    }

    updateFileInput() {
        // Create new DataTransfer object to update file input
        const dt = new DataTransfer();
        this.selectedFiles.forEach(file => {
            dt.items.add(file);
        });
        this.fileInput.files = dt.files;
    }

    showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed`;
        notification.style.cssText = `
            top: 20px;
            right: 20px;
            z-index: 9999;
            min-width: 300px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        `;
        
        notification.innerHTML = `
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

    // Public methods
    getFiles() {
        return this.selectedFiles;
    }

    clearFiles() {
        this.selectedFiles = [];
        this.updatePreview();
        this.updateFileInput();
    }

    addFiles(files) {
        this.handleFiles(files);
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Only initialize if we're on a page with drag-drop upload
    if (document.getElementById('dropZone')) {
        window.dragDropUpload = new DragDropUpload();
    }
});