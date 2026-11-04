// Petites aides de l'admin (tout fonctionne aussi sans JavaScript).

// Stock : « Remplir » met la même quantité dans toutes les pointures d'un coup
document.querySelectorAll('[data-fill-stock]').forEach((btn) =>
    btn.addEventListener('click', () => {
        const value = document.querySelector('[data-fill-value]')?.value ?? '';
        document.querySelectorAll('[data-stock-input]').forEach((input) => (input.value = value));
    }),
);

// Confirmation avant les actions irréversibles
document.addEventListener('submit', (e) => {
    const message = e.target.dataset.confirm;
    if (message && !confirm(message)) {
        e.preventDefault();
    }
});

// Couleur : le sélecteur et le code hexadécimal restent synchronisés
const hex = document.querySelector('[data-hex]');
const picker = document.querySelector('[data-hex-picker]');
if (hex && picker) {
    picker.addEventListener('input', () => (hex.value = picker.value));
    hex.addEventListener('input', () => /^#[0-9a-f]{6}$/i.test(hex.value) && (picker.value = hex.value));
}
