$(document).ready(function() {
    console.log('Profile page loaded');

    // Avatar preview
    $('#avatar').on('change', function(e) {
        var file = e.target.files[0];
        if(file) {
            // Validate file type
            var allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
            if(!allowedTypes.includes(file.type)) {
                alert('Hanya file JPG, JPEG, dan PNG yang diperbolehkan!');
                $(this).val('');
                return;
            }

            // Validate file size (max 1MB)
            if(file.size > 1024 * 1024) {
                alert('Ukuran file maksimal 1MB!');
                $(this).val('');
                return;
            }

            var reader = new FileReader();
            reader.onload = function(e) {
                // Remove existing preview
                $('.avatar-preview').remove();
                
                // Add new preview
                var preview = '<div class="avatar-preview mt-2">' +
                             '<img src="' + e.target.result + '" class="img-thumbnail rounded-circle" style="max-width: 150px; max-height: 150px;">' +
                             '<p class="small text-muted mt-1">Preview: ' + file.name + '</p>' +
                             '</div>';
                             
                $('#avatar').closest('.form-group').append(preview);
            };
            reader.readAsDataURL(file);
        }
    });

    // Profile form validation
    $('#profileForm').on('submit', function(e) {
        var name = $('#name').val().trim();
        var password = $('#password').val();
        var passwordConfirmation = $('#password_confirmation').val();

        // Validate name
        if(name.length < 2) {
            alert('Nama harus minimal 2 karakter!');
            e.preventDefault();
            return;
        }

        // Validate password if filled
        if(password && password.length < 8) {
            alert('Password harus minimal 8 karakter!');
            e.preventDefault();
            return;
        }

        // Validate password confirmation
        if(password && password !== passwordConfirmation) {
            alert('Konfirmasi password tidak cocok!');
            e.preventDefault();
            return;
        }

        // Show loading
        var submitBtn = $(this).find('button[type="submit"]');
        NShopApp.utils.showLoading(submitBtn);
    });

    // Password match indicator
    $('#password_confirmation').on('input', function() {
        var password = $('#password').val();
        var confirmation = $(this).val();
        
        $('.password-match').remove();
        
        if(confirmation.length > 0) {
            var isMatch = password === confirmation;
            var message = isMatch ? 
                '<small class="text-success password-match"><i class="fas fa-check"></i> Password cocok</small>' :
                '<small class="text-danger password-match"><i class="fas fa-times"></i> Password tidak cocok</small>';
            
            $(this).closest('.form-group').append(message);
        }
    });
});