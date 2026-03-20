import {library, dom} from '@fortawesome/fontawesome-svg-core';
import {fab} from '@fortawesome/free-brands-svg-icons';
import {fas} from '@fortawesome/free-solid-svg-icons';
import {far} from '@fortawesome/free-regular-svg-icons';

export default {
    init() {
        window.FontAwesomeConfig.searchPseudoElements = false;
        library.add(fab);
        library.add(fas);
        library.add(far);
        dom.watch();
    }
};