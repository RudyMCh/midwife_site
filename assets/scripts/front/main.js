import 'bootstrap';
import backgroundMenu from './_bgMenu.js';
import Burger from './burger.js';
import ShowContactFooter from './_showContactFooter.js';

document.addEventListener('DOMContentLoaded', () => {
    setTimeout(() => {
        backgroundMenu.init();
        Burger.init();
        ShowContactFooter.init();
    }, 200);
});
