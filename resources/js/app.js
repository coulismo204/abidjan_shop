// Améliorations du site public. Tout fonctionne aussi sans ce fichier (formulaires classiques).

// Apparition des éléments au défilement
const items = document.querySelectorAll('[data-reveal]');
if ('IntersectionObserver' in window) {
    const io = new IntersectionObserver(
        (entries) => {
            for (const entry of entries) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    io.unobserve(entry.target);
                }
            }
        },
        { rootMargin: '0px 0px -6% 0px', threshold: 0.1 },
    );
    items.forEach((el) => io.observe(el));
} else {
    items.forEach((el) => el.classList.add('is-visible'));
}

// En-tête : bordure une fois la page défilée
const header = document.querySelector('[data-header]');
if (header) {
    const onScroll = () => header.classList.toggle('is-scrolled', window.scrollY > 8);
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
}

// Filtres de la boutique : appliqués dès qu'on touche une option (sur ordinateur).
// Sur mobile, le panneau garde son bouton « Voir les résultats » pour choisir plusieurs filtres d'un coup.
const filters = document.querySelector('[data-filters]');
if (filters && matchMedia('(min-width: 1024px)').matches) {
    filters.addEventListener('change', (e) => {
        if (e.target.matches('input[type=checkbox], input[type=radio]')) {
            filters.requestSubmit();
        }
    });
}
document.querySelectorAll('[data-autosubmit]').forEach((el) =>
    el.addEventListener('change', () => el.form?.requestSubmit()),
);

// Fiche produit : galerie (vignettes → grande photo)
document.querySelectorAll('[data-gallery]').forEach((gallery) => {
    const main = gallery.querySelector('[data-gallery-main]');
    gallery.querySelectorAll('[data-gallery-thumb]').forEach((thumb) => {
        thumb.addEventListener('click', () => {
            main.src = thumb.dataset.src;
            gallery.querySelectorAll('[data-gallery-thumb]').forEach((t) => t.setAttribute('aria-current', 'false'));
            thumb.setAttribute('aria-current', 'true');
        });
    });
});

// Fiche produit : rappel de la pointure choisie sur la barre mobile
const sizePicker = document.querySelector('[data-size-picker]');
if (sizePicker) {
    const label = document.querySelector('[data-size-label]');
    sizePicker.addEventListener('change', (e) => {
        if (label && e.target.dataset.size) {
            label.textContent = e.target.dataset.size;
        }
    });
}

// Évite le double envoi des formulaires de commande
document.querySelectorAll('form[data-once]').forEach((form) =>
    form.addEventListener('submit', () => {
        setTimeout(() => form.querySelectorAll('button[type=submit]').forEach((b) => (b.disabled = true)), 0);
    }),
);
