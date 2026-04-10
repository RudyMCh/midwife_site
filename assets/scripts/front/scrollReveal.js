// Scroll reveal — anime les éléments .module à leur entrée dans le viewport.
// Requiert prefers-reduced-motion: no-preference (géré côté CSS).
const observer = new IntersectionObserver(
    (entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                observer.unobserve(entry.target);
            }
        });
    },
    { threshold: 0.1 }
);

const scrollReveal = {
    init() {
        document.querySelectorAll('.module').forEach((el) => observer.observe(el));
    },
};

export default scrollReveal;
