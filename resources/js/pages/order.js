$(document).ready(function() {
    console.log('Order page loaded');

    // Preview uploaded payment receipt image
    $('#payment_receipt').on('change', function(e) {
        var file = e.target.files[0];
        if(file) {
            // Validate file type
            var allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
            if(!allowedTypes.includes(file.type)) {
                alert('Hanya file JPG, JPEG, dan PNG yang diperbolehkan!');
                $(this).val('');
                return;
            }

            // Validate file size (max 2MB)
            if(file.size > 2 * 1024 * 1024) {
                alert('Ukuran file maksimal 2MB!');
                $(this).val('');
                return;
            }

            var reader = new FileReader();
            reader.onload = function(e) {
                // Remove existing preview
                $('.receipt-preview').remove();
                
                // Add new preview
                var preview = '<div class="receipt-preview mt-2">' +
                             '<img src="' + e.target.result + '" class="img-thumbnail" style="max-width: 200px;">' +
                             '<p class="small text-muted mt-1">Preview: ' + file.name + '</p>' +
                             '</div>';
                             
                $('#payment_receipt').closest('.form-group').append(preview);
            };
            reader.readAsDataURL(file);
        }
    });

    // Payment form submission
    $('#paymentForm').on('submit', function() {
        var submitBtn = $(this).find('button[type="submit"]');
        NShopApp.utils.showLoading(submitBtn);
    });

    // Confirm payment (admin)
    $('.confirm-payment-form').on('submit', function(e) {
        if(!confirm('Konfirmasi pembayaran untuk pesanan ini?')) {
            e.preventDefault();
        } else {
            var submitBtn = $(this).find('button[type="submit"]');
            NShopApp.utils.showLoading(submitBtn);
        }
    });
});