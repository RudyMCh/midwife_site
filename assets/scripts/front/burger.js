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
            menuTrigger?.setAttribute('aria-expanded', 'true');
            menuTrigger?.setAttribute('aria-label', 'Fermer le menu');
        }

        function closePushNav() {
            topNav.classList.remove('isOpen');
            openLevels.forEach(el => {
                el.nextElementSibling?.classList.remove('isOpen');
                el.setAttribute('aria-expanded', 'false');
            });
            document.body.classList.remove('pushNavIsOpen');
            menuTrigger?.setAttribute('aria-expanded', 'false');
            menuTrigger?.setAttribute('aria-label', 'Ouvrir le menu');
        }

        menuTrigger?.addEventListener('click', (e) => {
            e.preventDefault();
            topNav.classList.contains('isOpen') ? closePushNav() : openPushNav();
        });

        openLevels.forEach(el => el.addEventListener('click', () => {
            el.nextElementSibling?.classList.add('isOpen');
            el.setAttribute('aria-expanded', 'true');
        }));

        closeLevels.forEach(el => el.addEventListener('click', () => {
            const level = el.closest('.js-pushNavLevel');
            level?.classList.remove('isOpen');
            level?.previousElementSibling?.setAttribute('aria-expanded', 'false');
        }));

        closeLevelTops.forEach(el => el.addEventListener('click', () => closePushNav()));

        screen?.addEventListener('click', () => closePushNav());
    }
}

export default Burger;
