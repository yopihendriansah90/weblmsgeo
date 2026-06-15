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
