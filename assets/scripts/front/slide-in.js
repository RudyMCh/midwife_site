export default class SlideIn {
    static init() {
        this.slideIn();
    }

    static slideIn() {
        const modules = document.querySelectorAll('.module');
        if (!modules.length) {
            return;
        }

        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('come-in');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });

        modules.forEach((el) => {
            if (el.getBoundingClientRect().top < window.innerHeight) {
                el.classList.add('already-visible');
            } else {
                observer.observe(el);
            }
        });
    }
}
