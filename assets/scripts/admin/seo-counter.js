/**
 * Compteur de caractères pour les champs SEO.
 *
 * Activation : ajouter data-seo-min et data-seo-max sur l'input ou le textarea.
 *   <input data-seo-min="50" data-seo-max="60" ...>
 *
 * Indicateur couleur :
 *   - vert  : longueur dans la plage idéale [min, max]
 *   - orange : longueur acceptable mais hors plage (±30 % de tolérance)
 *   - rouge : trop court ou trop long
 */
export function initSeoCounters() {
    document.querySelectorAll('[data-seo-min][data-seo-max]').forEach((el) => {
        const min = parseInt(el.dataset.seoMin, 10);
        const max = parseInt(el.dataset.seoMax, 10);
        const tolerance = Math.round((max - min) * 1.5 + min * 0.2);

        const counter = document.createElement('div');
        counter.className = 'seo-counter mt-1';
        el.insertAdjacentElement('afterend', counter);

        function update() {
            const len = el.value.length;
            counter.textContent = `${len} / ${max} caractères`;

            counter.classList.remove('seo-counter--ok', 'seo-counter--warn', 'seo-counter--over');

            if (len >= min && len <= max) {
                counter.classList.add('seo-counter--ok');
            } else if (len > max && len <= max + tolerance) {
                counter.classList.add('seo-counter--warn');
            } else if (len > 0 && len < min && len >= min - tolerance) {
                counter.classList.add('seo-counter--warn');
            } else if (len === 0) {
                // champ vide : neutre
            } else {
                counter.classList.add('seo-counter--over');
            }
        }

        el.addEventListener('input', update);
        update();
    });
}
