<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\Contribution;
use App\Models\Expense;
use App\Models\Street;
use App\Models\User;
use App\Models\Villa;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Administrateur Quartier',
            'email' => 'admin@quartier.local',
            'password' => 'password',
            'role' => 'admin',
        ]);

        $streetNames = [
            'Rue des Palmiers',
            'Rue du Marche',
            'Avenue de la Paix',
            'Rue des Ecoles',
            'Rue du Jardin',
            'Avenue Centrale',
            'Rue des Manguiers',
            'Rue du Stade',
            'Impasse Bellevue',
            'Rue des Artisans',
        ];

        $streets = collect($streetNames)->map(fn ($name) => Street::create([
            'name' => $name,
            'description' => 'Secteur residentiel du quartier.',
        ]));

        $firstNames = ['Awa', 'Moussa', 'Fatou', 'Ibrahim', 'Aminata', 'Cheikh', 'Mariam', 'Ousmane', 'Ndeye', 'Abdou', 'Rama', 'Saliou', 'Adama', 'Bineta', 'Modou'];
        $lastNames = ['Diop', 'Ndiaye', 'Fall', 'Sow', 'Ba', 'Gueye', 'Diallo', 'Seck', 'Faye', 'Kane', 'Sarr', 'Sy', 'Lo', 'Mbaye', 'Cisse'];
        $paymentMethods = ['Especes', 'Orange Money', 'Wave', 'Virement bancaire'];

        $villas = collect();

        for ($i = 1; $i <= 100; $i++) {
            $firstName = $firstNames[($i - 1) % count($firstNames)];
            $lastName = $lastNames[($i * 3) % count($lastNames)];
            $street = $streets[($i - 1) % $streets->count()];

            $villas->push(Villa::create([
                'street_id' => $street->id,
                'number' => 'V-' . str_pad((string) $i, 3, '0', STR_PAD_LEFT),
                'owner_name' => $firstName . ' ' . $lastName,
                'owner_email' => strtolower($firstName . '.' . $lastName . $i . '@example.com'),
                'owner_phone' => '+22177' . str_pad((string) (1000000 + $i * 731), 7, '0', STR_PAD_LEFT),
                'notes' => $i % 12 === 0 ? 'Contact secondaire a verifier.' : null,
            ]));
        }

        $months = collect(range(0, 5))->map(fn ($offset) => Carbon::now()->startOfMonth()->subMonths($offset));

        foreach ($villas as $index => $villa) {
            foreach ($months as $monthIndex => $month) {
                if (($index + $monthIndex) % 9 === 0) {
                    continue;
                }

                Contribution::create([
                    'villa_id' => $villa->id,
                    'month' => $month->toDateString(),
                    'amount' => 10000 + (($index % 5) * 1000),
                    'paid_at' => $month->copy()->addDays(($index % 20) + 1)->toDateString(),
                    'payment_method' => $paymentMethods[($index + $monthIndex) % count($paymentMethods)],
                    'reference' => 'COT-' . $month->format('Ym') . '-' . str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT),
                    'notes' => null,
                ]);
            }
        }

        $expenses = [
            ['Nettoyage des rues', 75000, 'Nettoyage'],
            ['Reparation lampadaires', 125000, 'Eclairage'],
            ['Gardiennage mensuel', 180000, 'Securite'],
            ['Achat poubelles communes', 95000, 'Equipement'],
            ['Entretien espace vert', 60000, 'Jardin'],
            ['Peinture portail principal', 85000, 'Travaux'],
            ['Recharge compteur eau commun', 45000, 'Eau'],
            ['Communication et impressions', 30000, 'Administration'],
            ['Petites reparations voirie', 140000, 'Travaux'],
            ['Traitement anti-moustiques', 70000, 'Hygiene'],
        ];

        foreach ($expenses as $index => [$title, $amount, $category]) {
            Expense::create([
                'title' => $title,
                'amount' => $amount,
                'spent_at' => Carbon::now()->subDays($index * 8)->toDateString(),
                'category' => $category,
                'description' => 'Depense enregistree pour le fonctionnement du quartier.',
            ]);
        }

        Announcement::create([
            'title' => 'Rappel cotisation mensuelle',
            'destinataire' => 'Tous les proprietaires',
            'message' => 'Bonjour, merci de regler la cotisation du mois avant le 10.',
            'channel' => 'whatsapp',
            'target' => 'all',
            'sent_at' => now(),
        ]);

        Announcement::create([
            'title' => 'Journee de nettoyage',
            'destinataire' => 'Tous les proprietaires',
            'message' => 'Une journee de nettoyage est prevue samedi matin. Merci pour votre participation.',
            'channel' => 'email',
            'target' => 'all',
            'sent_at' => now()->subDays(7),
        ]);
    }
}
