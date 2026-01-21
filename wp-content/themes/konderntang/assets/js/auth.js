/**
 * Authentication Functions
 *
 * @package KonDernTang
 * @since 1.0.0
 */

(function() {
    'use strict';

    function switchAuthTab(tab) {
        const loginTab = document.getElementById('tab-login');
        const registerTab = document.getElementById('tab-register');
        const loginForm = document.getElementById('auth-login');
        const registerForm = document.getElementById('auth-register');
        
        if (tab === 'login') {
            if (loginTab) {
                loginTab.classList.add('border-b-2', 'border-primary', 'text-primary', 'bg-blue-50');
                loginTab.classList.remove('text-gray-600');
            }
            if (registerTab) {
                registerTab.classList.remove('border-b-2', 'border-primary', 'text-primary', 'bg-blue-50');
                registerTab.classList.add('text-gray-600');
            }
            if (loginForm) loginForm.classList.remove('hidden');
            if (registerForm) registerForm.classList.add('hidden');
        } else {
            if (registerTab) {
                registerTab.classList.add('border-b-2', 'border-primary', 'text-primary', 'bg-blue-50');
                registerTab.classList.remove('text-gray-600');
            }
            if (loginTab) {
                loginTab.classList.remove('border-b-2', 'border-primary', 'text-primary', 'bg-blue-50');
                loginTab.classList.add('text-gray-600');
            }
            if (registerForm) registerForm.classList.remove('hidden');
            if (loginForm) loginForm.classList.add('hidden');
        }
    }

    function handleLogin(event) {
        event.preventDefault();
        // In real app, this would authenticate user via WordPress
        alert('เข้าสู่ระบบสำเร็จ!');
    }

    function handleRegister(event) {
        event.preventDefault();
        // In real app, this would create user account via WordPress
        alert('สมัครสมาชิกสำเร็จ! กรุณาตรวจสอบอีเมลเพื่อยืนยันบัญชี');
    }

    // Expose functions globally
    window.switchAuthTab = switchAuthTab;
    window.handleLogin = handleLogin;
    window.handleRegister = handleRegister;

    // Initialize auth forms
    document.addEventListener('DOMContentLoaded', function() {
        const loginForm = document.getElementById('auth-login');
        const registerForm = document.getElementById('auth-register');
        
        if (loginForm) {
            loginForm.addEventListener('submit', handleLogin);
        }
        if (registerForm) {
            registerForm.addEventListener('submit', handleRegister);
        }
    });
})();
