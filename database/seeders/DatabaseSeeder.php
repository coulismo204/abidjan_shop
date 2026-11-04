<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('ADMIN_EMAIL', 'admin@abidjanshop.ci');

        if (! User::where('email', $email)->exists()) {
            $password = env('ADMIN_PASSWORD') ?: Str::password(14, symbols: false);
            User::create(['name' => 'Administrateur Abidjan Shop', 'email' => $email, 'password' => $password]);

            $this->command?->warn("Compte admin créé : {$email}");
            if (! env('ADMIN_PASSWORD')) {
                $this->command?->warn("Mot de passe généré : {$password}  (à changer dans « Mon compte »)");
            }
        }

        $this->call(CatalogSeeder::class);
    }
}
