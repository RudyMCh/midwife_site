import 'bootstrap';
import TomSelect from 'tom-select';
import ImagePreviewCompress from './imagePreviewCompress.js';
import { initSeoCounters } from './seo-counter.js';
import { initImageUploadHints } from './image-upload-hint.js';

document.addEventListener('DOMContentLoaded', () => {
    // Select multiple avec recherche (remplace Select2)
    document.querySelectorAll('.select2').forEach((el) => {
        new TomSelect(el, {
            plugins: ['remove_button'],
        });
    });

    // Éditeur rich text — TinyMCE chargé en script statique depuis /tinymce/tinymce.min.js
    if (typeof tinymce !== 'undefined') {
        tinymce.init({
            selector: 'textarea.tinymce',
            base_url: '/tinymce',
            suffix: '.min',
plugins: 'lists link code fullscreen table wordcount',
            toolbar: 'undo redo | bold italic | bullist numlist | link | table | code fullscreen',
            menubar: false,
            promotion: false,
            height: 400,
        });
    }

    // Prévisualisation compression image (uniquement sur la page concernée)
    if (document.getElementById('compressImage')) {
        ImagePreviewCompress.init();
    }

    // Compteurs de caractères sur les champs SEO
    initSeoCounters();

    // Aide au choix de photo : avertissement JS sur les dimensions
    initImageUploadHints();
});
