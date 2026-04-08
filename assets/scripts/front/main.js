import 'bootstrap';
import backgroundMenu from './_bgMenu';
import Burger from './burger';
import ShowContactFooter from './_showContactFooter';

document.addEventListener('DOMContentLoaded', () => {
    setTimeout(() => {
        backgroundMenu.init();
        Burger.init();
        ShowContactFooter.init();
    }, 200);
});
