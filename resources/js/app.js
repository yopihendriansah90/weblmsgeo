import './bootstrap';
import Swal from 'sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';

window.Swal = Swal;

const studentToast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 2800,
    timerProgressBar: true,
});

const notifyStudent = ({ type = 'success', message = '' } = {}) => {
    if (! message) {
        return;
    }

    studentToast.fire({
        icon: type,
        title: message,
    });
};

window.studentNotify = notifyStudent;

document.addEventListener('student-notify', (event) => notifyStudent(event.detail));

const setStudentNavState = (isOpen) => {
    const nav = document.getElementById('student-mobile-nav');
    const openButton = document.querySelector('[data-student-nav-open]');

    if (! nav) {
        return;
    }

    nav.classList.toggle('hidden', ! isOpen);
    nav.setAttribute('aria-hidden', String(! isOpen));
    openButton?.setAttribute('aria-expanded', String(isOpen));
    document.body.classList.toggle('overflow-hidden', isOpen);
};

document.addEventListener('click', (event) => {
    if (event.target.closest('[data-student-nav-open]')) {
        setStudentNavState(true);
        return;
    }

    if (event.target.closest('[data-student-nav-close], [data-student-nav-link]')) {
        setStudentNavState(false);
        return;
    }

    const passwordToggle = event.target.closest('[data-password-toggle]');

    if (passwordToggle) {
        const input = document.getElementById(passwordToggle.dataset.passwordToggle);
        const icon = passwordToggle.querySelector('.material-symbols-outlined');

        if (! input) {
            return;
        }

        const isPassword = input.type === 'password';
        input.type = isPassword ? 'text' : 'password';
        passwordToggle.setAttribute('aria-label', isPassword ? 'Sembunyikan password' : 'Tampilkan password');

        if (icon) {
            icon.textContent = isPassword ? 'visibility_off' : 'visibility';
        }
    }
});

document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
        setStudentNavState(false);
    }
});
