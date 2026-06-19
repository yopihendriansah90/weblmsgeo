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

window.guruNotify = notify;
window.guruToast = toast;

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

const openPreviewPlaceholder = () => {
    const previewTab = window.open('about:blank', '_blank');

    if (! previewTab) {
        return null;
    }

    previewTab.document.write(`
        <!doctype html>
        <title>Menyiapkan preview...</title>
        <body style="font-family: system-ui, sans-serif; padding: 24px; color: #334155;">
            Menyiapkan preview materi...
        </body>
    `);

    return previewTab;
};

const saveAndPreview = async (button) => {
    const componentId = button.dataset.componentId;
    const component = componentId && window.Livewire?.find
        ? window.Livewire.find(componentId)
        : null;

    if (! component) {
        notify({ type: 'error', message: 'Komponen form belum siap. Muat ulang halaman lalu coba lagi.' });
        return;
    }

    window.syncTinyMceEditors?.();

    const previewTab = openPreviewPlaceholder();
    const originalLabel = button.textContent;

    button.disabled = true;
    button.textContent = 'Menyiapkan...';

    try {
        const previewUrl = await component.call('saveAndPreview');

        if (previewTab) {
            previewTab.location.href = previewUrl;
        } else {
            window.open(previewUrl, '_blank');
        }
    } catch (error) {
        previewTab?.close();
        notify({ type: 'error', message: 'Preview belum bisa dibuka. Periksa kembali isian bab.' });
    } finally {
        button.disabled = false;
        button.textContent = originalLabel;
    }
};

document.addEventListener('guru-notify', (event) => notify(event.detail));
document.addEventListener('guru-confirm-delete', (event) => confirmDelete(event.detail));
document.addEventListener('click', (event) => {
    const button = event.target.closest('[data-save-preview-button]');

    if (! button) {
        return;
    }

    event.preventDefault();
    saveAndPreview(button);
});
