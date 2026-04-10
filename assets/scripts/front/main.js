import 'bootstrap';
import backgroundMenu from './_bgMenu.js';
import Burger from './burger.js';
import ShowContactFooter from './_showContactFooter.js';
import scrollReveal from './scrollReveal.js';

document.addEventListener('DOMContentLoaded', () => {
    backgroundMenu.init();
    Burger.init();
    ShowContactFooter.init();
    scrollReveal.init();
});
