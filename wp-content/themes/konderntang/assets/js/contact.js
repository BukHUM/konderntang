/**
 * Contact Form Handler
 *
 * @package KonDernTang
 * @since 1.0.0
 */

(function() {
    'use strict';

    function handleContactSubmit(event) {
        event.preventDefault();
        
        const form = event.target;
        const formData = new FormData(form);
        
        // Get WordPress AJAX URL and nonce
        const ajaxUrl = window.konderntangData?.ajaxUrl || '/wp-admin/admin-ajax.php';
        const nonce = window.konderntangData?.nonce || '';
        
        // Submit via AJAX
        fetch(ajaxUrl, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('ขอบคุณสำหรับข้อความของคุณ! เราจะติดต่อกลับโดยเร็วที่สุด');
                form.reset();
            } else {
                alert('เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง');
        });
    }

    // Initialize contact form
    document.addEventListener('DOMContentLoaded', function() {
        const contactForm = document.getElementById('contact-form');
        if (contactForm) {
            contactForm.addEventListener('submit', handleContactSubmit);
        }
    });
})();
