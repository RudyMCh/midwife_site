import 'bootstrap';
import '../../styles/admin/main.scss';
import TomSelect from 'tom-select';
import tinymce from 'tinymce/tinymce';
import 'tinymce/themes/silver/theme';
import 'tinymce/icons/default/icons';
import 'tinymce/plugins/lists/plugin';
import 'tinymce/plugins/link/plugin';
import 'tinymce/plugins/code/plugin';
import 'tinymce/plugins/fullscreen/plugin';
import 'tinymce/plugins/table/plugin';
import 'tinymce/plugins/wordcount/plugin';
import ImagePreviewCompress from './imagePreviewCompress';

document.addEventListener('DOMContentLoaded', () => {
    // Select multiple avec recherche (remplace Select2)
    document.querySelectorAll('.select2').forEach((el) => {
        new TomSelect(el, {
            plugins: ['remove_button'],
        });
    });

    // Éditeur rich text (remplace CKEditor)
    tinymce.init({
        selector: 'textarea.tinymce',
        base_url: '/build/tinymce',
        suffix: '.min',
        language: 'fr_FR',
        plugins: 'lists link code fullscreen table wordcount',
        toolbar: 'undo redo | bold italic | bullist numlist | link | table | code fullscreen',
        menubar: false,
        promotion: false,
        height: 400,
    });

    // Prévisualisation compression image (uniquement sur la page concernée)
    if (document.getElementById('compressImage')) {
        ImagePreviewCompress.init();
    }
});
