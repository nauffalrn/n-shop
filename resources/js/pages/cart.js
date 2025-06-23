$(document).ready(function() {
    console.log('Cart page loaded');

    // Quantity buttons
    $('.qty-btn').on('click', function() {
        var action = $(this).data('action');
        var input = $(this).closest('.input-group').find('.qty-input');
        var currentVal = parseInt(input.val());
        var max = parseInt(input.attr('max'));
        
        if(action === 'increase' && currentVal < max) {
            input.val(currentVal + 1);
        } else if(action === 'decrease' && currentVal > 1) {
            input.val(currentVal - 1);
        }
        
        // Auto submit form
        input.closest('.quantity-form').submit();
    });

    // Manual input change
    $('.qty-input').on('change', function() {
        $(this).closest('.quantity-form').submit();
    });

    // Update cart amount
    $('.update-cart-form').on('submit', function(e) {
        var submitBtn = $(this).find('button[type="submit"]');
        NShopApp.utils.showLoading(submitBtn);
    });

    // Delete cart item with confirmation
    $('.delete-cart-form').on('submit', function(e) {
        if(!confirm('Yakin ingin menghapus item ini dari keranjang?')) {
            e.preventDefault();
        }
    });
});