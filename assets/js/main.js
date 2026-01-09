// assets/js/main.js
// Main JavaScript untuk aplikasi

$(document).ready(function() {
    
    // ========================================
    // GENERAL FUNCTIONS
    // ========================================
    
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        $('.alert').not('.alert-permanent').fadeOut('slow', function() {
            $(this).remove();
        });
    }, 5000);
    
    // Confirm delete action
    $('.delete-confirm').on('click', function(e) {
        if (!confirm('Apakah Anda yakin ingin menghapus item ini?')) {
            e.preventDefault();
            return false;
        }
    });
    
    // Format number input as currency
    $('.currency-input').on('keyup', function() {
        let value = $(this).val().replace(/[^0-9]/g, '');
        if (value) {
            $(this).val(formatRupiah(value));
        }
    });
    
    // Number only input
    $('.number-only').on('keypress', function(e) {
        const charCode = (e.which) ? e.which : e.keyCode;
        if (charCode > 31 && (charCode < 48 || charCode > 57)) {
            e.preventDefault();
            return false;
        }
        return true;
    });
    
    // ========================================
    // SEARCH & FILTER
    // ========================================
    
    // Table search
    $('#searchTable').on('keyup', function() {
        const value = $(this).val().toLowerCase();
        $('#dataTable tbody tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });
    
    // Live search with debounce
    let searchTimeout;
    $('.live-search').on('keyup', function() {
        clearTimeout(searchTimeout);
        const searchInput = $(this);
        
        searchTimeout = setTimeout(function() {
            const query = searchInput.val();
            if (query.length >= 3) {
                // Implement your search logic here
                console.log('Searching for: ' + query);
            }
        }, 500);
    });
    
    // ========================================
    // FORM VALIDATION
    // ========================================
    
    // Basic form validation
    $('form.needs-validation').on('submit', function(e) {
        if (!this.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
        }
        $(this).addClass('was-validated');
    });
    
    // Password confirmation validation
    $('.confirm-password').on('keyup', function() {
        const password = $('#password').val();
        const confirmPassword = $(this).val();
        
        if (password !== confirmPassword) {
            $(this).addClass('is-invalid');
            $('.password-match-error').show();
        } else {
            $(this).removeClass('is-invalid').addClass('is-valid');
            $('.password-match-error').hide();
        }
    });
    
    // ========================================
    // IMAGE UPLOAD PREVIEW
    // ========================================
    
    $('.image-upload').on('change', function(e) {
        const file = e.target.files[0];
        const previewId = $(this).data('preview');
        
        if (file) {
            // Validate file type
            const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            if (!validTypes.includes(file.type)) {
                alert('Tipe file tidak valid. Hanya JPG, PNG, dan GIF yang diperbolehkan.');
                $(this).val('');
                return;
            }
            
            // Validate file size (max 2MB)
            if (file.size > 2 * 1024 * 1024) {
                alert('Ukuran file terlalu besar. Maksimal 2MB.');
                $(this).val('');
                return;
            }
            
            // Show preview
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#' + previewId).attr('src', e.target.result).show();
            };
            reader.readAsDataURL(file);
        }
    });
    
    // ========================================
    // DATATABLES (if needed)
    // ========================================
    
    if ($.fn.DataTable) {
        $('.datatable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json"
            },
            "pageLength": 10,
            "responsive": true
        });
    }
    
    // ========================================
    // TOOLTIPS & POPOVERS
    // ========================================
    
    // Initialize Bootstrap tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Initialize Bootstrap popovers
    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(function(popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
    
    // ========================================
    // QUANTITY CONTROLS
    // ========================================
    
    $('.qty-increment').on('click', function() {
        const input = $(this).siblings('.qty-input');
        let value = parseInt(input.val()) || 0;
        const max = parseInt(input.attr('max')) || 999;
        
        if (value < max) {
            input.val(value + 1).trigger('change');
        }
    });
    
    $('.qty-decrement').on('click', function() {
        const input = $(this).siblings('.qty-input');
        let value = parseInt(input.val()) || 0;
        const min = parseInt(input.attr('min')) || 0;
        
        if (value > min) {
            input.val(value - 1).trigger('change');
        }
    });
    
    // ========================================
    // MODAL AUTO FOCUS
    // ========================================
    
    $('.modal').on('shown.bs.modal', function() {
        $(this).find('[autofocus]').focus();
    });
    
    // ========================================
    // SMOOTH SCROLL
    // ========================================
    
    $('a[href^="#"]').on('click', function(e) {
        const target = $(this.getAttribute('href'));
        if (target.length) {
            e.preventDefault();
            $('html, body').stop().animate({
                scrollTop: target.offset().top - 80
            }, 1000);
        }
    });
    
    // ========================================
    // PRINT FUNCTIONALITY
    // ========================================
    
    $('.print-button').on('click', function(e) {
        e.preventDefault();
        window.print();
    });
    
    // ========================================
    // LOADING OVERLAY
    // ========================================
    
    function showLoading() {
        $('body').append('<div class="spinner-overlay"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>');
    }
    
    function hideLoading() {
        $('.spinner-overlay').remove();
    }
    
    // Show loading on AJAX requests
    $(document).ajaxStart(function() {
        showLoading();
    }).ajaxStop(function() {
        hideLoading();
    });
    
    // ========================================
    // COPY TO CLIPBOARD
    // ========================================
    
    $('.copy-to-clipboard').on('click', function() {
        const text = $(this).data('copy');
        navigator.clipboard.writeText(text).then(function() {
            alert('Berhasil disalin ke clipboard!');
        }).catch(function(err) {
            console.error('Gagal menyalin:', err);
        });
    });
    
    // ========================================
    // HELPER FUNCTIONS
    // ========================================
    
    // Format number as Rupiah
    window.formatRupiah = function(angka, prefix = 'Rp ') {
        const numberString = angka.toString().replace(/[^,\d]/g, '');
        const split = numberString.split(',');
        const sisa = split[0].length % 3;
        let rupiah = split[0].substr(0, sisa);
        const ribuan = split[0].substr(sisa).match(/\d{3}/gi);
        
        if (ribuan) {
            const separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }
        
        rupiah = split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;
        return prefix + rupiah;
    };
    
    // Format date
    window.formatDate = function(date) {
        const d = new Date(date);
        const day = ('0' + d.getDate()).slice(-2);
        const month = ('0' + (d.getMonth() + 1)).slice(-2);
        const year = d.getFullYear();
        return `${day}/${month}/${year}`;
    };
    
    // Debounce function
    window.debounce = function(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    };
    
    // ========================================
    // NOTIFICATION
    // ========================================
    
    window.showNotification = function(message, type = 'info') {
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show position-fixed top-0 end-0 m-3" role="alert" style="z-index: 9999;">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        $('body').append(alertHtml);
        
        setTimeout(function() {
            $('.alert').fadeOut('slow', function() {
                $(this).remove();
            });
        }, 5000);
    };
    
    // ========================================
    // AUTO-SAVE FORM (Draft)
    // ========================================
    
    const autoSaveForms = $('.auto-save-form');
    if (autoSaveForms.length > 0) {
        autoSaveForms.find('input, textarea, select').on('change', debounce(function() {
            const formData = $(this).closest('form').serialize();
            localStorage.setItem('form_draft_' + $(this).closest('form').attr('id'), formData);
            console.log('Form auto-saved');
        }, 2000));
    }
    
    // ========================================
    // SIDEBAR TOGGLE (if applicable)
    // ========================================
    
    $('.sidebar-toggle').on('click', function() {
        $('body').toggleClass('sidebar-collapsed');
    });
    
    // ========================================
    // BACK TO TOP BUTTON
    // ========================================
    
    $(window).scroll(function() {
        if ($(this).scrollTop() > 200) {
            $('.back-to-top').fadeIn();
        } else {
            $('.back-to-top').fadeOut();
        }
    });
    
    $('.back-to-top').on('click', function(e) {
        e.preventDefault();
        $('html, body').animate({scrollTop: 0}, 800);
    });
    
});

// ========================================
// CONSOLE MESSAGE
// ========================================

console.log('%c🛒 Sistem Kasir PHP', 'font-size: 20px; font-weight: bold; color: #1E3A8A;');
console.log('%cDeveloped with ❤️', 'font-size: 12px; color: #666;');
