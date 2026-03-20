class Burger{
    constructor() {
    }
    static init() {
        this.menuBurger();
    }
    static menuBurger() {
        let x = window.matchMedia("(max-width: 768px)");
        if (x.matches) { // If media query matches
            let $menuTrigger = $('.js-menuToggle');
            let $topNav = $('.js-topPushNav');
            let $openLevel = $('.js-openLevel');
            let $closeLevel = $('.js-closeLevel');
            let $closeLevelTop = $('.js-closeLevelTop');
            let $navLevel = $('.js-pushNavLevel');

            function openPushNav() {
                $topNav.addClass('isOpen');
                $('body').addClass('pushNavIsOpen');
            }

            function closePushNav() {
                $topNav.removeClass('isOpen');
                $openLevel.siblings().removeClass('isOpen');
                $('body').removeClass('pushNavIsOpen');
            }

            $menuTrigger.on('click touchstart', function (e) {
                console.log("menu on")
                e.preventDefault();
                if ($topNav.hasClass('isOpen')) {
                    closePushNav();
                } else {
                    openPushNav();
                }
            });

            $openLevel.on('click touchstart', function () {
                $(this).next($navLevel).addClass('isOpen');
            });

            $closeLevel.on('click touchstart', function () {
                $(this).closest($navLevel).removeClass('isOpen');
            });

            $closeLevelTop.on('click touchstart', function () {
                closePushNav();
            });

            $('.screen').click(function () {
                closePushNav();
            });
        }
    }
}
export default Burger