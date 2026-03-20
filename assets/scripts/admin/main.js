import $ from 'jquery';
import 'bootstrap';
import '../../styles/admin/main.scss';
import 'select2';
import utils from "../utils";
import ImagePreviewCompress from "./imagePreviewCompress";


$(document).ready(()=>{
    setTimeout(()=>{
        // utils.init();
        $('.select2').select2();
        ImagePreviewCompress.init();

    }, 200);
})