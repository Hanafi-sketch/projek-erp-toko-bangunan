import './bootstrap';

import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();

// Inisialisasi Select2 dari CDN (jQuery dan Select2 harus dimuat di layout)
document.addEventListener('DOMContentLoaded', () => {
    // Pastikan jQuery dan Select2 tersedia dari CDN
    if (typeof window.$ !== 'undefined' && typeof $.fn.select2 === 'function') {
        const $select = $('#penjualanSelect');
        if ($select.length) {
            $select.select2({
                theme: 'bootstrap4',
                placeholder: "-- Cari Data Penjualan --",
                allowClear: true,
                width: '100%'
            });
        }
    } else {
        console.warn('jQuery atau Select2 tidak tersedia.');
    }
});
