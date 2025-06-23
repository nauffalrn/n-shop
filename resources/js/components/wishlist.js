window.WishlistComponent = {
    init: function() {
        this.bindEvents();
    },
    
    bindEvents: function() {
        $(document).on('click', '.wishlist-btn, .wishlist-remove-btn', this.handleWishlistAction);
    },
    
    handleWishlistAction: function(e) {
        e.preventDefault();
        
        var btn = $(this);
        var productId = btn.data('product-id');
        var wishlistId = btn.data('wishlist-id');
        var isRemove = btn.hasClass('wishlist-remove-btn');
        
        if(isRemove && !confirm('Yakin ingin menghapus item ini dari wishlist?')) {
            return;
        }
        
        var url = wishlistId ? '/wishlist/' + wishlistId : '/wishlist/' + productId + '/toggle';
        var method = wishlistId ? 'DELETE' : 'POST';
        
        $.ajax({
            url: url,
            type: method,
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if(isRemove) {
                    // Remove from wishlist page
                    btn.closest('.card').fadeOut(function() {
                        $(this).remove();
                        if($('.product-card').length === 0) {
                            location.reload();
                        }
                    });
                } else {
                    // Toggle wishlist in product pages
                    if(response.status === 'added') {
                        btn.find('i').removeClass('text-muted').addClass('text-danger');
                        btn.attr('title', 'Hapus dari Wishlist');
                    } else {
                        btn.find('i').removeClass('text-danger').addClass('text-muted');
                        btn.attr('title', 'Tambah ke Wishlist');
                    }
                }
            },
            error: function() {
                alert('Terjadi kesalahan. Silakan coba lagi.');
            }
        });
    }
};

// Initialize when document ready
$(document).ready(function() {
    WishlistComponent.init();
});