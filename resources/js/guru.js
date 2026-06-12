import './bootstrap';
import './tinymce';
import '../css/guru.css';
import Swal from 'sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';

window.Swal = Swal;

const toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 2800,
    timerProgressBar: true,
});

const notify = ({ type = 'success', message = '' } = {}) => {
    if (! message) {
        return;
    }

    toast.fire({
        icon: type,
        title: message,
    });
};

const confirmDelete = async (detail = {}) => {
    const {
        title = 'Hapus data ini?',
        text = 'Tindakan ini tidak bisa dibatalkan.',
        action,
        id,
        componentId,
        confirmButtonText = 'Ya, hapus',
    } = detail;

    if (! action || typeof id === 'undefined' || ! componentId || ! window.Livewire?.find) {
        return;
    }

    const result = await Swal.fire({
        title,
        text,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText,
        cancelButtonText: 'Batal',
        reverseButtons: true,
    });

    if (! result.isConfirmed) {
        return;
    }

    const component = window.Livewire.find(componentId);

    if (! component) {
        return;
    }

    await component.call(action, id);
};

document.addEventListener('guru-notify', (event) => notify(event.detail));
document.addEventListener('guru-confirm-delete', (event) => confirmDelete(event.detail));
