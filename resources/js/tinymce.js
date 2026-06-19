import tinymce from 'tinymce/tinymce';
import 'tinymce/icons/default/icons';
import 'tinymce/models/dom/model';
import 'tinymce/themes/silver';
import 'tinymce/plugins/advlist';
import 'tinymce/plugins/autolink';
import 'tinymce/plugins/code';
import 'tinymce/plugins/image';
import 'tinymce/plugins/link';
import 'tinymce/plugins/lists';
import 'tinymce/plugins/table';
import 'tinymce/plugins/wordcount';

const initEditor = (textarea) => {
    if (textarea.dataset.tinymceReady === '1') return;

    const syncInput = document.getElementById(textarea.dataset.syncTarget);
    if (!syncInput) return;

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    textarea.dataset.tinymceReady = '1';

    tinymce.init({
        target: textarea,
        base_url: '/tinymce',
        license_key: 'gpl',
        menubar: false,
        skin_url: '/tinymce/skins/ui/oxide',
        content_css: '/tinymce/skins/ui/oxide/content.min.css',
        height: 640,
        min_height: 520,
        plugins: 'advlist autolink code image link lists table wordcount',
        toolbar: [
            'undo redo | blocks | bold italic underline | alignleft aligncenter alignright alignjustify',
            'bullist numlist outdent indent | link image table | removeformat code',
        ].join(' | '),
        block_formats: 'Paragraf=p; Judul 1=h1; Judul 2=h2; Judul 3=h3; Kutipan=blockquote',
        image_advtab: true,
        image_caption: true,
        object_resizing: true,
        images_upload_handler: (blobInfo, progress) => new Promise((resolve, reject) => {
            const formData = new FormData();
            formData.append('file', blobInfo.blob(), blobInfo.filename());

            const xhr = new XMLHttpRequest();
            xhr.open('POST', '/guru/editor-image-upload');
            xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
            xhr.setRequestHeader('Accept', 'application/json');

            xhr.upload.onprogress = (event) => {
                if (event.lengthComputable) {
                    progress((event.loaded / event.total) * 100);
                }
            };

            xhr.onload = () => {
                if (xhr.status < 200 || xhr.status >= 300) {
                    reject(`Upload gagal: ${xhr.status}`);
                    return;
                }

                try {
                    const json = JSON.parse(xhr.responseText);
                    if (!json.location) {
                        reject('Response upload tidak valid.');
                        return;
                    }

                    resolve(json.location);
                } catch (error) {
                    reject('Response upload bukan JSON valid.');
                }
            };

            xhr.onerror = () => reject(`Upload gagal: ${xhr.status}`);
            xhr.send(formData);
        }),
        setup: (editor) => {
            editor.on('init', () => {
                editor.setContent(syncInput.value || textarea.value || '');
            });

            editor.on('change keyup undo redo', () => {
                syncInput.value = editor.getContent();
                syncInput.dispatchEvent(new Event('input', { bubbles: true }));
            });
        },
    });
};

const boot = () => {
    document.querySelectorAll('[data-tinymce]').forEach(initEditor);
};

document.addEventListener('DOMContentLoaded', boot);
document.addEventListener('livewire:navigated', boot);
document.addEventListener('livewire:load', boot);
