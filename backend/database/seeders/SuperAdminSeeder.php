<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\SuperAdministrateur;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Eviter les doublons
        if (!User::where('email', 'superadmin@primemarket.sn')->exists()) {
            $user = User::create([
                'nom' => 'Admin',
                'prenom' => 'Super',
                'email' => 'superadmin@primemarket.sn',
                'password' => Hash::make('SuperAdmin@2026'),
                'role' => 'super_administrateur',
                'statut' => 'actif',
                'telephone' => '+221770000000',
                'adresse' => 'Dakar, Sénégal',
            ]);

            SuperAdministrateur::create([
                'id' => $user->id,
            ]);
        }
    }
}
