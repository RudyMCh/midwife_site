export default class ShowContactFooter {
    static init() {
        this.setup();
    }

    static setup() {
        const buttons = document.querySelectorAll('.footer-midwife-name');
        buttons.forEach(btn => {
            btn.addEventListener('click', () => {
                const isOpen = btn.getAttribute('aria-expanded') === 'true';

                // Ferme tous les autres
                buttons.forEach(other => {
                    other.setAttribute('aria-expanded', 'false');
                    other.nextElementSibling?.classList.remove('display');
                });

                // Bascule l'actif
                if (!isOpen) {
                    btn.setAttribute('aria-expanded', 'true');
                    btn.nextElementSibling?.classList.add('display');
                }
            });
        });
    }
}
