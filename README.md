# ABIDJAN SHOP : chaussures homme en ligne

Boutique en ligne de chaussures pour homme à Abidjan : catalogue filtrable, panier sans compte,
commande enregistrée puis récapitulatif envoyé sur WhatsApp. Paiement à la livraison ou Mobile Money.
Laravel 13, Blade + Tailwind CSS 4, MySQL. Aucun service payant requis.

## Fonctionnement

- **Catalogue** (`/chaussures`) : filtres par pointure, marque, couleur, tranche de prix et promo, recherche,
  tri. Tout passe par l'URL (formulaire GET) : fonctionne sans JavaScript et se partage sur WhatsApp.
  Seuls les produits ayant au moins une pointure en stock sont listés.
- **Fiche produit** : choix de la pointure (épuisées barrées), autres coloris du même modèle,
  « Commander maintenant » (va directement à la commande) ou « Ajouter au panier ».
- **Commande** (`/commande`) : nom, téléphone (format ivoirien vérifié), commune, quartier/repère, mode de
  paiement. Le stock est réservé dans une transaction (`app/Actions/PlaceOrder.php`), puis la page de
  confirmation propose d'envoyer le récapitulatif sur WhatsApp (message dans `app/Support/WhatsApp.php`).
- **Produits sans photo** : un dessin de chaussure à la couleur du produit est affiché (`components/shoe`),
  le catalogue est présentable dès le premier jour.
- **Admin** (`/admin`) : commandes (statuts Nouvelle → Confirmée → Livrée, ou Annulée qui remet le stock),
  produits (stock par pointure, photos converties en WebP, « ajouter un coloris »), catégories, marques,
  couleurs, réglages (numéro WhatsApp, frais de livraison, textes), tableau de bord (ventes, stock faible).

## Installation locale

```bash
composer install && npm install
cp .env.example .env && php artisan key:generate
# créer la base MySQL « abidjan_shop » puis renseigner DB_USERNAME / DB_PASSWORD dans .env
php artisan migrate --seed      # crée le compte admin + un catalogue d'exemple
php artisan storage:link
npm run build                   # ou `npm run dev` pendant le développement
php artisan serve               # http://localhost:8000 , admin : /admin
```

Le seeder affiche le mot de passe admin généré (ou utilise `ADMIN_EMAIL` / `ADMIN_PASSWORD` du `.env`).
Le catalogue d'exemple (prix indicatifs) n'est créé que si aucun produit n'existe.

Tests : `php artisan test` (base PostgreSQL locale `abidjan_shop_test`, voir `phpunit.xml`).

## Mise en ligne (hébergement mutualisé PHP 8.3+, MySQL)

1. En local : `composer install --no-dev --optimize-autoloader` puis `npm run build`.
2. Envoyer le projet (avec `vendor/` et `public/build/`, sans `node_modules/`) **hors** du dossier web,
   et faire pointer le domaine vers `public/`.
3. `.env` de production : `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://…`, accès MySQL,
   puis `php artisan key:generate`.
4. `php artisan migrate --seed --force`, `php artisan storage:link`,
   `php artisan config:cache && php artisan route:cache && php artisan view:cache`.
5. Dans l'admin : vrai numéro WhatsApp, frais de livraison, puis remplacer les produits d'exemple.

## À personnaliser

- Charte (orange, vert vif, jaune, bleu ciel, fonds acidulés ; texte noir, contrastes WCAG vérifiés) : `resources/css/app.css`.
- Logo texte : `resources/views/components/logo.blade.php` et `public/favicon.svg`.
- Textes de la page « Livraison et paiement » (FAQ, guide des pointures) : `resources/views/public/how.blade.php`.
