class Burger {
    static init() {
        this.menuBurger();
    }

    static menuBurger() {
        if (!window.matchMedia('(max-width: 768px)').matches) {
            return;
        }

        const menuTrigger = document.querySelector('.js-menuToggle');
        const topNav = document.querySelector('.js-topPushNav');
        const openLevels = document.querySelectorAll('.js-openLevel');
        const closeLevels = document.querySelectorAll('.js-closeLevel');
        const closeLevelTops = document.querySelectorAll('.js-closeLevelTop');
        const screen = document.querySelector('.screen');

        function openPushNav() {
            topNav.classList.add('isOpen');
            document.body.classList.add('pushNavIsOpen');
        }

        function closePushNav() {
            topNav.classList.remove('isOpen');
            openLevels.forEach(el => el.nextElementSibling?.classList.remove('isOpen'));
            document.body.classList.remove('pushNavIsOpen');
        }

        menuTrigger?.addEventListener('click', (e) => {
            e.preventDefault();
            topNav.classList.contains('isOpen') ? closePushNav() : openPushNav();
        });

        openLevels.forEach(el => el.addEventListener('click', () => {
            el.nextElementSibling?.classList.add('isOpen');
        }));

        closeLevels.forEach(el => el.addEventListener('click', () => {
            el.closest('.js-pushNavLevel')?.classList.remove('isOpen');
        }));

        closeLevelTops.forEach(el => el.addEventListener('click', () => closePushNav()));

        screen?.addEventListener('click', () => closePushNav());
    }
}

export default Burger;
