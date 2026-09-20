<?php

namespace Database\Seeders;

use App\Enums\MerchantStatus;
use App\Enums\PublishStatus;
use App\Enums\UserRole;
use App\Models\Category;
use App\Models\Deal;
use App\Models\Event;
use App\Models\EventType;
use App\Models\Form;
use App\Models\Merchant;
use App\Models\Page;
use App\Models\Post;
use App\Models\Service;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'info@ldmedia.be'],
            ['name' => 'Raphaël Degand', 'password' => Hash::make('password'), 'role' => UserRole::SuperAdmin]
        );
        User::query()->updateOrCreate(
            ['email' => 'president@athinfo.be'],
            ['name' => 'Comité ACA', 'password' => Hash::make('password'), 'role' => UserRole::Admin]
        );

        Setting::put('admin_email', 'president@athinfo.be');
        Setting::put('hero_image', '/images/hero/ath.jpg');
        Setting::put('address', 'Rue Ernest Cambier 2/1, 7800 Ath');
        Setting::put('facebook', 'https://www.facebook.com/aca.commercantsdath/');

        $categories = [];
        foreach ([
            ['mode', 'Mode', 'Mode', 'Fashion', '#5B3A7A', 1],
            ['alimentation', 'Alimentation', 'Voeding', 'Food', '#C4A035', 2],
            ['horeca', 'Horeca', 'Horeca', 'Horeca', '#C45B8A', 3],
            ['beaute', 'Beauté & bien-être', 'Schoonheid & welzijn', 'Beauty & wellness', '#7A5A3A', 4],
            ['maison', 'Maison & déco', 'Wonen & deco', 'Home & decor', '#3A5B7A', 5],
            ['services', 'Services', 'Diensten', 'Services', '#4A6B4A', 6],
            ['culture', 'Culture & loisirs', 'Cultuur & vrije tijd', 'Culture & leisure', '#6B3A4A', 7],
        ] as [$key, $fr, $nl, $en, $color, $pos]) {
            $slugs = config("aca.category_slugs.$key");
            $categories[$key] = Category::query()->updateOrCreate(
                ['icon' => $key],
                [
                    'name' => compact('fr', 'nl', 'en'),
                    'slug' => ['fr' => $slugs['fr'], 'nl' => $slugs['nl'], 'en' => $slugs['en']],
                    'pin_color' => $color,
                    'position' => $pos,
                ]
            );
        }

        $services = [];
        foreach ([
            ['terrasse', 'Terrasse', 'Terras', 'Terrace'],
            ['parking', 'Parking', 'Parking', 'Parking'],
            ['pmr', 'Accès PMR', 'Toegankelijk', 'Wheelchair access'],
            ['livraison', 'Livraison', 'Levering', 'Delivery'],
            ['bancontact', 'Bancontact', 'Bancontact', 'Bancontact'],
            ['cheques', 'Chèques-cadeaux', 'Cadeaucheques', 'Gift vouchers'],
        ] as $i => [$key, $fr, $nl, $en]) {
            $services[$key] = Service::query()->updateOrCreate(['key' => $key], [
                'name' => compact('fr', 'nl', 'en'),
                'position' => $i,
            ]);
        }

        $types = [];
        foreach ([
            ['braderie', 'Braderie', 'Uitverkoop', 'Street sale', '#C4A035'],
            ['animation', 'Animation', 'Animatie', 'Animation', '#5B3A7A'],
            ['degustation', 'Dégustation', 'Proeverij', 'Tasting', '#C45B8A'],
            ['marche', 'Marché', 'Markt', 'Market', '#4A6B4A'],
            ['soldes', 'Soldes', 'Solden', 'Sales', '#3A5B7A'],
            ['sport', 'Sport', 'Sport', 'Sport', '#2F6B4F'],
            ['culture', 'Culture', 'Cultuur', 'Culture', '#3A5B7A'],
            ['foire', 'Foire', 'Beurs', 'Fair', '#8B5A2B'],
        ] as [$slug, $fr, $nl, $en, $color]) {
            $types[$slug] = EventType::query()->updateOrCreate(['slug' => $slug], [
                'name' => compact('fr', 'nl', 'en'),
                'color' => $color,
            ]);
        }

        $photos = collect(range(1, 8))->map(fn ($n) => '/images/shops/'.$n.'.jpg')->all();

        $shops = [
            [
                'name' => 'Le Comptoir de Basile',
                'category' => 'alimentation',
                'address' => 'Rue Ernest Cambier 6',
                'postal_code' => '7800',
                'city' => 'Ath',
                'lat' => 50.62945,
                'lng' => 3.77685,
                'phone' => '068 65 73 34',
                'email' => 'olivier@lecomptoirdebasile.be',
                'website' => 'https://lecomptoirdebasile.be',
                'cover' => '/images/shops/basile.jpg',
                'services' => ['bancontact'],
                'featured' => true,
                'sunday' => false,
                'social' => [['facebook', 'https://www.facebook.com/lecomptoirdebasile']],
                'hours' => [
                    2 => [['10:00', '18:30']],
                    3 => [['10:00', '18:30']],
                    4 => [['09:00', '18:30']],
                    5 => [['10:00', '20:00']],
                    6 => [['09:00', '18:30']],
                ],
                'fr' => 'Fromagerie et épicerie fine au Comptoir de Basile, rue Ernest Cambier. Fromages de saison, produits du terroir et conseils pour composer un plateau.',
                'highlights' => 'Plateaux à emporter, produits locaux, fermé dimanche et lundi.',
            ],
            [
                'name' => 'LD Media',
                'category' => 'services',
                'address' => 'Rue Ernest Cambier 9/1',
                'postal_code' => '7800',
                'city' => 'Ath',
                'lat' => 50.62952,
                'lng' => 3.77705,
                'phone' => '0476 32 32 75',
                'email' => 'info@ldmedia.be',
                'website' => 'https://ldmedia.be',
                'cover' => '/images/shops/ld-media.jpg',
                'services' => ['parking', 'bancontact'],
                'featured' => true,
                'sunday' => false,
                'social' => [['facebook', 'https://www.facebook.com/ldmedia.be'], ['instagram', 'https://www.instagram.com/ldmedia.be']],
                'hours' => [
                    1 => [['09:00', '18:00']],
                    2 => [['09:00', '18:00']],
                    3 => [['09:00', '18:00']],
                    4 => [['09:00', '18:00']],
                    5 => [['09:00', '17:00']],
                ],
                'fr' => 'Agence de communication à Ath et Chièvres : sites web, identité visuelle, photo, vidéo et campagnes pour les commerces et institutions de la région.',
                'highlights' => 'Aussi à Chièvres, Rue de Saint-Ghislain 12A.',
            ],
            [
                'name' => 'Servibati',
                'category' => 'services',
                'address' => 'Rue de la Sille 6',
                'postal_code' => '7822',
                'city' => 'Meslin-l’Évêque',
                'lat' => 50.6398,
                'lng' => 3.8084,
                'phone' => '068 80 01 00',
                'email' => 'info@servibati.be',
                'website' => 'https://www.servibati.be',
                'cover' => '/images/shops/servibati.jpg',
                'services' => ['parking', 'bancontact'],
                'featured' => false,
                'sunday' => false,
                'social' => [['facebook', 'https://www.facebook.com/servibati']],
                'hours' => [
                    1 => [['08:00', '17:00']],
                    2 => [['08:00', '17:00']],
                    3 => [['08:00', '17:00']],
                    4 => [['08:00', '17:00']],
                    5 => [['08:00', '16:00']],
                ],
                'fr' => 'Protection incendie, extincteurs, signalisation et maintenance pour les commerces, écoles et entreprises de l’Athois.',
                'highlights' => 'Interventions sur Ath et communes voisines.',
            ],
            [
                'name' => 'La Maison des Plantes',
                'category' => 'maison',
                'address' => 'Rue de Pintamont 11',
                'postal_code' => '7800',
                'city' => 'Ath',
                'lat' => 50.62895,
                'lng' => 3.77605,
                'phone' => '068 28 60 57',
                'email' => null,
                'website' => null,
                'cover' => '/images/shops/maison-plantes.jpg',
                'services' => ['bancontact', 'cheques'],
                'featured' => false,
                'sunday' => false,
                'social' => [],
                'hours' => [
                    2 => [['09:30', '18:00']],
                    3 => [['09:30', '18:00']],
                    4 => [['09:30', '18:00']],
                    5 => [['09:30', '18:00']],
                    6 => [['09:30', '18:00']],
                ],
                'fr' => 'Herboristerie athoise rue de Pintamont : plantes, tisanes, huiles essentielles et conseils pour le quotidien.',
                'highlights' => 'Produits naturels, conseil en boutique.',
            ],
            [
                'name' => 'Yves Rocher Ath',
                'category' => 'beaute',
                'address' => 'Rue Ernest Cambier 2',
                'postal_code' => '7800',
                'city' => 'Ath',
                'lat' => 50.6292232,
                'lng' => 3.7765245,
                'phone' => '068 44 92 22',
                'email' => null,
                'website' => 'https://www.yves-rocher.be',
                'cover' => '/images/shops/yves-rocher.jpg',
                'services' => ['bancontact', 'cheques'],
                'featured' => true,
                'sunday' => false,
                'social' => [['facebook', 'https://www.facebook.com/YvesRocherBelgique']],
                'hours' => [
                    2 => [['09:30', '18:00']],
                    3 => [['09:30', '18:00']],
                    4 => [['09:30', '18:00']],
                    5 => [['09:30', '18:00']],
                    6 => [['09:30', '18:00']],
                ],
                'fr' => 'Institut et boutique Yves Rocher au cœur d’Ath, rue Ernest Cambier : soins visage, maquillage et conseils beauté botanique.',
                'highlights' => 'Fermé dimanche et lundi. Soins sur rendez-vous.',
            ],
            [
                'name' => 'Paris Parfums',
                'category' => 'beaute',
                'address' => 'Rue Ernest Cambier 10-12',
                'postal_code' => '7800',
                'city' => 'Ath',
                'lat' => 50.62962,
                'lng' => 3.77718,
                'phone' => '068 28 20 48',
                'email' => 'info@parisparfums.be',
                'website' => 'https://www.parisparfums.be',
                'cover' => '/images/shops/paris-parfums.jpg',
                'services' => ['bancontact', 'cheques'],
                'featured' => true,
                'sunday' => false,
                'social' => [['facebook', 'https://www.facebook.com/parisparfumsath']],
                'hours' => [
                    1 => [['14:00', '18:00']],
                    2 => [['09:00', '18:00']],
                    3 => [['09:00', '18:00']],
                    4 => [['09:00', '18:00']],
                    5 => [['09:00', '18:00']],
                    6 => [['09:00', '18:00']],
                ],
                'fr' => 'Parfumerie indépendante à Ath : sélections de parfums, soins et cadeaux, à deux pas de la Grand-Place.',
                'highlights' => 'Ouvert le lundi après-midi.',
            ],
            [
                'name' => 'Bella Donna',
                'category' => 'mode',
                'address' => 'Rue Ernest Cambier 9',
                'postal_code' => '7800',
                'city' => 'Ath',
                'lat' => 50.62955,
                'lng' => 3.77702,
                'phone' => '0493 35 49 02',
                'email' => 'belladonna.ath@outlook.fr',
                'website' => 'https://belladonnaath.com',
                'cover' => '/images/shops/bella-donna.jpg',
                'services' => ['bancontact', 'cheques'],
                'featured' => true,
                'sunday' => false,
                'social' => [['instagram', 'https://www.instagram.com/belladonna.ath'], ['facebook', 'https://www.facebook.com/belladonnaath']],
                'hours' => [
                    2 => [['10:00', '18:00']],
                    3 => [['10:00', '18:00']],
                    4 => [['10:00', '18:00']],
                    5 => [['10:00', '18:00']],
                    6 => [['10:00', '18:00']],
                ],
                'fr' => 'Boutique de mode féminine rue Ernest Cambier : collections actuelles, conseil en cabine et pièces pour toutes les envies athoises.',
                'highlights' => 'Mode femme, au centre-ville.',
            ],
            [
                'name' => 'Bubbles Jump',
                'category' => 'culture',
                'address' => 'Place des Capucins 21',
                'postal_code' => '7800',
                'city' => 'Ath',
                'lat' => 50.62815,
                'lng' => 3.78055,
                'phone' => '068 33 92 16',
                'email' => null,
                'website' => 'https://www.bubblesjump.be',
                'cover' => '/images/shops/1.jpg',
                'services' => ['parking', 'bancontact'],
                'featured' => false,
                'sunday' => true,
                'social' => [],
                'hours' => [1 => [['10:00', '18:30']], 2 => [['10:00', '18:30']], 3 => [['10:00', '18:30']], 4 => [['10:00', '18:30']], 5 => [['10:00', '18:30']], 6 => [['10:00', '18:30']], 7 => [['10:00', '18:00']]],
                'fr' => 'Parc de jeux indoor pour les enfants, anniversaires et après-midis en famille, place des Capucins.',
                'highlights' => 'Chaussettes obligatoires, jusqu’à 12 ans.',
            ],
            [
                'name' => 'Tout Simplement Lui',
                'category' => 'mode',
                'address' => 'Rue de Nazareth 7',
                'postal_code' => '7800',
                'city' => 'Ath',
                'lat' => 50.63145,
                'lng' => 3.77595,
                'phone' => null,
                'email' => null,
                'website' => null,
                'cover' => '/images/shops/2.jpg',
                'services' => ['bancontact', 'cheques'],
                'featured' => false,
                'sunday' => false,
                'social' => [],
                'hours' => [2 => [['09:30', '18:00']], 3 => [['09:30', '18:00']], 4 => [['09:30', '18:00']], 5 => [['09:30', '18:00']], 6 => [['09:30', '18:00']]],
                'fr' => 'Prêt-à-porter masculin au centre d’Ath : pièces du quotidien, conseil taille et styles pour tous les âges.',
                'highlights' => 'Mode homme, rue de Nazareth.',
            ],
            [
                'name' => 'Atome',
                'category' => 'mode',
                'address' => 'Quai Saint-Jacques 9',
                'postal_code' => '7800',
                'city' => 'Ath',
                'lat' => 50.63105,
                'lng' => 3.77945,
                'phone' => null,
                'email' => null,
                'website' => null,
                'cover' => '/images/shops/3.jpg',
                'services' => ['bancontact', 'cheques'],
                'featured' => false,
                'sunday' => false,
                'social' => [],
                'hours' => [2 => [['10:00', '18:00']], 3 => [['10:00', '18:00']], 4 => [['10:00', '18:00']], 5 => [['10:00', '18:00']], 6 => [['10:00', '18:00']]],
                'fr' => 'Vêtements, bijoux, accessoires et déco conçus de façon plus responsable, quai Saint-Jacques.',
                'highlights' => 'Créateurs et pièces responsables.',
            ],
            [
                'name' => 'Herboristerie Alara',
                'category' => 'alimentation',
                'address' => 'Rue Ernest Cambier 4',
                'postal_code' => '7800',
                'city' => 'Ath',
                'lat' => 50.62932,
                'lng' => 3.77662,
                'phone' => null,
                'email' => 'herboristerieath@gmail.com',
                'website' => 'https://www.herboristerieath.be',
                'cover' => '/images/shops/maison-plantes.jpg',
                'services' => ['bancontact'],
                'featured' => false,
                'sunday' => false,
                'social' => [],
                'hours' => [2 => [['10:00', '18:00']], 3 => [['10:00', '18:00']], 4 => [['10:00', '18:00']], 5 => [['10:00', '18:00']], 6 => [['10:00', '18:00']]],
                'fr' => 'Herboristerie, épicerie naturelle et conseils bien-être rue Ernest Cambier, au cœur d’Ath.',
                'highlights' => 'Mardi à samedi, 10h-18h.',
            ],
            [
                'name' => 'La Pouponnière',
                'category' => 'mode',
                'address' => 'Rue Ernest Cambier 5',
                'postal_code' => '7800',
                'city' => 'Ath',
                'lat' => 50.62938,
                'lng' => 3.77672,
                'phone' => null,
                'email' => null,
                'website' => null,
                'cover' => '/images/shops/4.jpg',
                'services' => ['bancontact', 'cheques'],
                'featured' => false,
                'sunday' => false,
                'social' => [],
                'hours' => [2 => [['09:30', '18:00']], 3 => [['09:30', '18:00']], 4 => [['09:30', '18:00']], 5 => [['09:30', '18:00']], 6 => [['09:30', '18:00']]],
                'fr' => 'Puériculture et mode enfantine de 0 à 14 ans, rue Ernest Cambier.',
                'highlights' => 'Bébé, enfant, cadeaux de naissance.',
            ],
            [
                'name' => 'Jolies Folies',
                'category' => 'mode',
                'address' => 'Rue aux Gades 32',
                'postal_code' => '7800',
                'city' => 'Ath',
                'lat' => 50.63042,
                'lng' => 3.77785,
                'phone' => null,
                'email' => null,
                'website' => null,
                'cover' => '/images/shops/bella-donna.jpg',
                'services' => ['bancontact', 'cheques'],
                'featured' => false,
                'sunday' => false,
                'social' => [],
                'hours' => [2 => [['10:00', '18:00']], 3 => [['10:00', '18:00']], 4 => [['10:00', '18:00']], 5 => [['10:00', '18:00']], 6 => [['10:00', '18:00']]],
                'fr' => 'Boutique de mode et accessoires rue aux Gades, pour se faire plaisir au détour d’une vitrine athoise.',
                'highlights' => 'Mode femme et accessoires.',
            ],
            [
                'name' => 'Ikone',
                'category' => 'mode',
                'address' => 'Grand-Place 8',
                'postal_code' => '7800',
                'city' => 'Ath',
                'lat' => 50.63078,
                'lng' => 3.77682,
                'phone' => null,
                'email' => null,
                'website' => null,
                'cover' => '/images/shops/5.jpg',
                'services' => ['bancontact', 'cheques'],
                'featured' => false,
                'sunday' => false,
                'social' => [],
                'hours' => [2 => [['10:00', '18:00']], 3 => [['10:00', '18:00']], 4 => [['10:00', '18:00']], 5 => [['10:00', '18:00']], 6 => [['10:00', '18:00']]],
                'fr' => 'Concept store de mode sur la Grand-Place : silhouettes actuelles et pièces coup de cœur.',
                'highlights' => 'Grand-Place d’Ath.',
            ],
            [
                'name' => 'Ath’mosphère',
                'category' => 'maison',
                'address' => 'Rue de Pintamont 18',
                'postal_code' => '7800',
                'city' => 'Ath',
                'lat' => 50.62892,
                'lng' => 3.77585,
                'phone' => null,
                'email' => null,
                'website' => null,
                'cover' => '/images/shops/6.jpg',
                'services' => ['bancontact'],
                'featured' => false,
                'sunday' => false,
                'social' => [],
                'hours' => [2 => [['09:30', '18:00']], 3 => [['09:30', '18:00']], 4 => [['09:30', '18:00']], 5 => [['09:30', '18:00']], 6 => [['09:30', '18:00']]],
                'fr' => 'Déco, ambiance et objets pour la maison, pour habiller l’intérieur comme les vitrines du centre-ville.',
                'highlights' => 'Maison & déco.',
            ],
            [
                'name' => 'Couleur Urbaine',
                'category' => 'maison',
                'address' => 'Rue de Bruxelles 15',
                'postal_code' => '7800',
                'city' => 'Ath',
                'lat' => 50.63162,
                'lng' => 3.77852,
                'phone' => null,
                'email' => null,
                'website' => null,
                'cover' => '/images/shops/7.jpg',
                'services' => ['parking', 'bancontact'],
                'featured' => false,
                'sunday' => false,
                'social' => [],
                'hours' => [2 => [['09:00', '18:00']], 3 => [['09:00', '18:00']], 4 => [['09:00', '18:00']], 5 => [['09:00', '18:00']], 6 => [['09:00', '17:00']]],
                'fr' => 'Peintures, conseils couleur et fournitures pour habiller murs et vitrines athoises.',
                'highlights' => 'Couleur et matériaux.',
            ],
            [
                'name' => 'Profil BD',
                'category' => 'culture',
                'address' => 'Rue Ernest Cambier 17',
                'postal_code' => '7800',
                'city' => 'Ath',
                'lat' => 50.6294968,
                'lng' => 3.7763996,
                'phone' => '068 84 00 77',
                'email' => null,
                'website' => 'https://www.facebook.com/profilbdath/',
                'cover' => '/images/shops/8.jpg',
                'services' => ['bancontact'],
                'featured' => false,
                'sunday' => false,
                'social' => [['facebook', 'https://www.facebook.com/profilbdath/']],
                'hours' => [2 => [['10:00', '18:00']], 3 => [['10:00', '18:00']], 4 => [['10:00', '18:00']], 5 => [['10:00', '18:00']], 6 => [['10:00', '18:00']]],
                'fr' => 'Librairie spécialisée BD, mangas et comics, plus de 6000 références rue Ernest Cambier.',
                'highlights' => 'Conseils lecteurs, jeunes et collectionneurs.',
            ],
            [
                'name' => 'Centre Équilibre',
                'category' => 'beaute',
                'address' => 'Rue des Récollets 10',
                'postal_code' => '7800',
                'city' => 'Ath',
                'lat' => 50.62958,
                'lng' => 3.77542,
                'phone' => null,
                'email' => null,
                'website' => null,
                'cover' => '/images/shops/yves-rocher.jpg',
                'services' => ['bancontact'],
                'featured' => false,
                'sunday' => false,
                'social' => [],
                'hours' => [1 => [['18:00', '21:00']], 2 => [['18:00', '21:00']], 3 => [['18:00', '21:00']], 4 => [['18:00', '21:00']], 5 => [['18:00', '21:00']]],
                'fr' => 'Bien-être, soins et équilibre au centre-ville, pour un rendez-vous après le travail.',
                'highlights' => 'Soins en soirée.',
            ],
            [
                'name' => 'Frip’ Lover',
                'category' => 'mode',
                'address' => 'Rue du Pont 8',
                'postal_code' => '7800',
                'city' => 'Ath',
                'lat' => 50.63012,
                'lng' => 3.77848,
                'phone' => null,
                'email' => null,
                'website' => null,
                'cover' => '/images/shops/2.jpg',
                'services' => ['bancontact'],
                'featured' => false,
                'sunday' => false,
                'social' => [],
                'hours' => [2 => [['10:00', '18:00']], 3 => [['10:00', '18:00']], 4 => [['10:00', '18:00']], 5 => [['10:00', '18:00']], 6 => [['10:00', '18:00']]],
                'fr' => 'Friperie et seconde main : chiner, renouveler sa garde-robe et donner une seconde vie aux pièces.',
                'highlights' => 'Mode circulaire.',
            ],
            [
                'name' => 'PersonnalisATHion',
                'category' => 'services',
                'address' => 'Rue du Moulin 2',
                'postal_code' => '7800',
                'city' => 'Ath',
                'lat' => 50.63005,
                'lng' => 3.77485,
                'phone' => '0494 42 79 90',
                'email' => 'contact@personnalisathion.be',
                'website' => 'https://personnalisathion.be',
                'cover' => '/images/shops/ld-media.jpg',
                'services' => ['bancontact'],
                'featured' => false,
                'sunday' => false,
                'social' => [],
                'hours' => [1 => [['10:00', '12:00'], ['13:30', '17:30']], 2 => [['10:00', '12:00'], ['13:30', '17:30']], 3 => [['10:00', '12:00'], ['13:30', '17:30']], 4 => [['10:00', '12:00'], ['13:30', '17:30']], 5 => [['10:00', '12:00'], ['13:30', '17:30']]],
                'fr' => 'Atelier de personnalisation : marquage textile, gravure, impression et cadeaux sur mesure depuis 2020.',
                'highlights' => 'Sur rendez-vous à l’atelier.',
            ],
            [
                'name' => 'Au Bon Coin',
                'category' => 'alimentation',
                'address' => 'Grand-Place 22',
                'postal_code' => '7800',
                'city' => 'Ath',
                'lat' => 50.63105,
                'lng' => 3.77725,
                'phone' => null,
                'email' => null,
                'website' => null,
                'cover' => '/images/shops/basile.jpg',
                'services' => ['bancontact'],
                'featured' => false,
                'sunday' => true,
                'social' => [],
                'hours' => [1 => [['08:00', '20:00']], 2 => [['08:00', '20:00']], 3 => [['08:00', '20:00']], 4 => [['08:00', '20:00']], 5 => [['08:00', '20:00']], 6 => [['08:00', '20:00']], 7 => [['08:00', '13:00']]],
                'fr' => 'Épicerie de proximité sur la Grand-Place : courses du quotidien, ouvert tôt et tard.',
                'highlights' => 'Horaires élargis.',
            ],
            [
                'name' => 'Lusitanos',
                'category' => 'horeca',
                'address' => 'Rue de la Station 9',
                'postal_code' => '7800',
                'city' => 'Ath',
                'lat' => 50.62918,
                'lng' => 3.78012,
                'phone' => null,
                'email' => null,
                'website' => null,
                'cover' => '/images/shops/3.jpg',
                'services' => ['terrasse', 'bancontact'],
                'featured' => false,
                'sunday' => false,
                'social' => [],
                'hours' => [2 => [['10:00', '14:30'], ['18:00', '22:00']], 3 => [['10:00', '14:30'], ['18:00', '22:00']], 4 => [['10:00', '14:30'], ['18:00', '22:00']], 5 => [['10:00', '14:30'], ['18:00', '22:00']], 6 => [['10:00', '22:00']]],
                'fr' => 'Table portugaise à Ath : grillades, bacalhau et convivialité, à deux pas de la gare.',
                'highlights' => 'Horeca, soirées du week-end.',
            ],
            [
                'name' => 'La Maison du Lion',
                'category' => 'horeca',
                'address' => 'Grand-Place 1',
                'postal_code' => '7800',
                'city' => 'Ath',
                'lat' => 50.63072,
                'lng' => 3.77648,
                'phone' => null,
                'email' => null,
                'website' => null,
                'cover' => '/images/shops/4.jpg',
                'services' => ['terrasse', 'bancontact', 'pmr'],
                'featured' => false,
                'sunday' => true,
                'social' => [],
                'hours' => [1 => [['11:00', '22:00']], 2 => [['11:00', '22:00']], 3 => [['11:00', '22:00']], 4 => [['11:00', '22:00']], 5 => [['11:00', '23:00']], 6 => [['11:00', '23:00']], 7 => [['11:00', '22:00']]],
                'fr' => 'Adresse de la Grand-Place, entre terrasse, brasserie et rendez-vous des Athois.',
                'highlights' => 'Face aux géants.',
            ],
            [
                'name' => 'Brasserie des Légendes',
                'category' => 'alimentation',
                'address' => 'Chemin de l’Ermite 19',
                'postal_code' => '7801',
                'city' => 'Irchonwelz',
                'lat' => 50.6168,
                'lng' => 3.8102,
                'phone' => '068 28 20 07',
                'email' => null,
                'website' => 'https://www.brasseriedeslegendes.be',
                'cover' => '/images/shops/5.jpg',
                'services' => ['parking', 'bancontact'],
                'featured' => false,
                'sunday' => false,
                'social' => [],
                'hours' => [1 => [['09:00', '17:00']], 2 => [['09:00', '17:00']], 3 => [['09:00', '17:00']], 4 => [['09:00', '17:00']], 5 => [['09:00', '17:00']]],
                'fr' => 'Brasserie athoise à Irchonwelz : bières de légende, boutique et visites autour du houblon local.',
                'highlights' => 'Goudale, Athoise et millésimes.',
            ],
            [
                'name' => 'Palette',
                'category' => 'beaute',
                'address' => 'Rue aux Gades 12',
                'postal_code' => '7800',
                'city' => 'Ath',
                'lat' => 50.63035,
                'lng' => 3.77755,
                'phone' => null,
                'email' => null,
                'website' => null,
                'cover' => '/images/shops/paris-parfums.jpg',
                'services' => ['bancontact'],
                'featured' => false,
                'sunday' => false,
                'social' => [],
                'hours' => [2 => [['09:00', '18:00']], 3 => [['09:00', '18:00']], 4 => [['09:00', '18:00']], 5 => [['09:00', '18:00']], 6 => [['09:00', '17:00']]],
                'fr' => 'Institut et beauté en centre-ville, pour un teint frais entre deux courses sur la Grand-Place.',
                'highlights' => 'Soins et rendez-vous.',
            ],
            [
                'name' => 'Le Stock Ath',
                'category' => 'maison',
                'address' => 'Rue de la Station 28',
                'postal_code' => '7800',
                'city' => 'Ath',
                'lat' => 50.62855,
                'lng' => 3.78115,
                'phone' => null,
                'email' => null,
                'website' => null,
                'cover' => '/images/shops/6.jpg',
                'services' => ['parking', 'bancontact'],
                'featured' => false,
                'sunday' => false,
                'social' => [],
                'hours' => [1 => [['09:00', '18:00']], 2 => [['09:00', '18:00']], 3 => [['09:00', '18:00']], 4 => [['09:00', '18:00']], 5 => [['09:00', '18:00']], 6 => [['09:00', '16:00']]],
                'fr' => 'Destockage et bonnes affaires pour la maison, à deux pas de la gare.',
                'highlights' => 'Prix stock.',
            ],
            [
                'name' => 'GN Picts',
                'category' => 'culture',
                'address' => 'Rue de Pintamont 22',
                'postal_code' => '7800',
                'city' => 'Ath',
                'lat' => 50.62888,
                'lng' => 3.77555,
                'phone' => null,
                'email' => null,
                'website' => null,
                'cover' => '/images/shops/7.jpg',
                'services' => ['bancontact'],
                'featured' => false,
                'sunday' => false,
                'social' => [],
                'hours' => [2 => [['11:00', '18:30']], 3 => [['11:00', '18:30']], 4 => [['11:00', '18:30']], 5 => [['11:00', '18:30']], 6 => [['10:00', '18:30']]],
                'fr' => 'Jeux, figurines et univers GN : une adresse pour les passionnés de plateaux et de mondes imaginaires.',
                'highlights' => 'Jeux de société et figurines.',
            ],
            [
                'name' => 'Pierre O’Green',
                'category' => 'maison',
                'address' => 'Rue de France 11',
                'postal_code' => '7800',
                'city' => 'Ath',
                'lat' => 50.63255,
                'lng' => 3.77945,
                'phone' => null,
                'email' => null,
                'website' => null,
                'cover' => '/images/shops/maison-plantes.jpg',
                'services' => ['livraison', 'bancontact'],
                'featured' => false,
                'sunday' => false,
                'social' => [],
                'hours' => [2 => [['09:00', '18:00']], 3 => [['09:00', '18:00']], 4 => [['09:00', '18:00']], 5 => [['09:00', '18:00']], 6 => [['09:00', '17:00']]],
                'fr' => 'Plantes, jardin et conseils verts pour balcons, cours et vitrines athoises.',
                'highlights' => 'Végétal en ville.',
            ],
        ];

        foreach ($shops as $i => $shop) {
            $slug = Str::slug($shop['name']);
            $merchant = Merchant::query()->updateOrCreate(['name' => $shop['name']], [
                'slug' => ['fr' => $slug, 'nl' => $slug, 'en' => $slug],
                'description' => ['fr' => $shop['fr'], 'nl' => $shop['fr'], 'en' => $shop['fr']],
                'short_text' => ['fr' => $shop['highlights'], 'nl' => $shop['highlights'], 'en' => $shop['highlights']],
                'highlights' => ['fr' => $shop['highlights'], 'nl' => $shop['highlights'], 'en' => $shop['highlights']],
                'address' => $shop['address'],
                'postal_code' => $shop['postal_code'],
                'city' => $shop['city'],
                'lat' => $shop['lat'],
                'lng' => $shop['lng'],
                'phone' => $shop['phone'],
                'email' => $shop['email'],
                'website' => $shop['website'],
                'status' => MerchantStatus::Published,
                'is_featured' => $shop['featured'],
                'member_since' => now()->subMonths(10 - ($i % 5)),
                'open_sundays' => $shop['sunday'],
                'holiday_closed' => true,
                'cover_url' => $shop['cover'],
            ]);
            $merchant->categories()->sync([$categories[$shop['category']]->id]);
            $merchant->services()->sync(array_map(fn ($k) => $services[$k]->id, $shop['services']));
            $merchant->openingHours()->delete();
            foreach ($shop['hours'] as $day => $slots) {
                foreach ($slots as $p => [$open, $close]) {
                    $merchant->openingHours()->create([
                        'weekday' => $day,
                        'opens_at' => $open,
                        'closes_at' => $close,
                        'position' => $p,
                    ]);
                }
            }
            $merchant->memberships()->updateOrCreate(['year' => now()->year], ['status' => 'paid', 'paid_on' => now()->subMonths(2), 'amount' => 120]);
            $merchant->socialLinks()->delete();
            foreach ($shop['social'] as [$network, $url]) {
                $merchant->socialLinks()->create(['network' => $network, 'url' => $url]);
            }
        }

        $keep = collect($shops)->pluck('name');
        Merchant::query()->whereNotIn('name', $keep)->each(function (Merchant $merchant) {
            $merchant->deals()->delete();
            $merchant->openingHours()->delete();
            $merchant->socialLinks()->delete();
            $merchant->memberships()->delete();
            $merchant->closures()->delete();
            $merchant->categories()->detach();
            $merchant->services()->detach();
            $merchant->delete();
        });

        $formContact = $this->form('contact', 'Contact', [
            ['text', 'Nom', true, null, 'half'],
            ['email', 'Email', true, null, 'half'],
            ['select', 'Raison du contact', true, "Question\nPartenariat\nPresse\nAdhésion\nAutre"],
            ['textarea', 'Message', true],
        ]);
        $categoryOptions = collect($categories)->map(fn (Category $category) => $category->t('name', 'fr'))->implode("\n");
        $formJoin = $this->form('join', 'Devenir membre', [
            ['text', 'Raison sociale', true, null, 'half'],
            ['text', 'N° d’entreprise (BCE)', true, null, 'half'],
            ['text', 'Rue et numéro', true],
            ['text', 'Code postal', true, null, 'half'],
            ['text', 'Localité', true, null, 'half'],
            ['text', 'Téléphone', true, null, 'half'],
            ['email', 'Email', true, null, 'half'],
            ['text', 'Personne de contact', true],
            ['checkbox', 'Catégorie', false, $categoryOptions],
            ['text', 'Autre catégorie', false],
        ]);
        $formEvent = $this->form('event', 'Proposer un événement', [
            ['text', 'Titre', true],
            ['date', 'Date', true],
            ['text', 'Lieu', true],
            ['textarea', 'Description', true],
            ['email', 'Email', true],
        ]);
        $formDeal = $this->form('deal', 'Proposer un bon plan', [
            ['text', 'Commerce', true],
            ['text', 'Titre', true],
            ['textarea', 'Description', true],
            ['date', 'Date de fin', true],
            ['email', 'Email', true],
        ]);
        $this->form('suggest', 'Suggérer une modification', [
            ['text', 'Nom', true],
            ['email', 'Email', true],
            ['textarea', 'Modification', true],
        ]);
        $this->form('register', 'Inscription événement', [
            ['text', 'Nom', true],
            ['email', 'Email', true],
            ['number', 'Nombre de personnes', true],
        ]);

        $events = [
            [
                'Les 24 Heures à Pied d’Ath — 50e édition',
                'sport',
                'CEVA et centre-ville',
                Carbon::create(2026, 9, 18, 8, 0),
                Carbon::create(2026, 9, 20, 18, 0),
                '50e édition des 24 Heures à Pied d’Ath, avec concert gratuit des Poulycrocs le vendredi 18 septembre dès 21 h. Une partie du parcours des 6 Heures à pied du dimanche traverse le centre-ville, dans le cadre de la Journée sans voiture. Source : La Vie Athoise n°182, septembre 2026.',
                '/images/events/sport.jpg',
                false,
                'https://www.24h-ath.be',
            ],
            [
                'Belgian Vespa Days Ath',
                'animation',
                'Hall CEVA',
                Carbon::create(2026, 9, 19, 7, 30),
                Carbon::create(2026, 9, 20, 15, 0),
                'Rassemblement au CEVA : accueil, concours de lenteur, balades et gymkhana le samedi ; grande parade à travers Ath le dimanche à 10 h 30, puis animations. Source : La Vie Athoise n°182.',
                '/images/events/vespa.jpg',
                false,
                null,
            ],
            [
                'Journée sans voiture',
                'animation',
                'Centre-ville d’Ath',
                Carbon::create(2026, 9, 20, 10, 0),
                Carbon::create(2026, 9, 20, 18, 0),
                'Le centre-ville d’Ath se transforme en espace piéton et cyclable de 10 h à 18 h : animations, stands, foodtrucks et commerces ouverts. Source : La Vie Athoise n°182 / Ville d’Ath.',
                '/images/hero/ath.jpg',
                false,
                'https://www.ath.be',
            ],
            [
                'La Gouyasse',
                'sport',
                'Hall CEVA',
                Carbon::create(2026, 9, 27, 10, 0),
                Carbon::create(2026, 9, 27, 12, 0),
                'Course ACRHO au cœur du Pays Vert : La Gouyasse 12,4 km à 10 h et La P’tite Gouyasse 6,5 km à 10 h 10. Départ et arrivée au Hall CEVA. Inscription sur place dès 8 h 30 (6 € / 5 € affiliés). Source : La Vie Athoise n°182.',
                '/images/events/gouyasse.jpg',
                false,
                null,
            ],
            [
                'Floralies d’Ath',
                'animation',
                'Maison des Géants, rue de Pintamont 18',
                Carbon::create(2026, 10, 2, 10, 0),
                Carbon::create(2026, 10, 11, 17, 0),
                'Première édition des Floralies d’Ath : parcours floral à la Maison des Géants, fleuristes, horticulteurs, artisans et commerçants (dont l’ACA parmi les partenaires). Nocturne le 9 octobre. 8 € adulte, 5 € enfant. Source : La Vie Athoise n°182 et maisondesgeants.be.',
                '/images/events/floralies.jpg',
                true,
                'https://maisondesgeants.be/les-floralies/',
            ],
            [
                '63e Fête de la Bière à Ormeignies',
                'degustation',
                'Salle Spoculo, Ormeignies',
                Carbon::create(2026, 10, 3, 19, 0),
                Carbon::create(2026, 10, 4, 18, 0),
                'La Royale Fanfare Saint-Ursmer d’Ormeignies organise sa 63e fête de la bière. Samedi : souper sur réservation puis soirée Oberbayern. Dimanche : festival musical dès 14 h. Entrée gratuite. Source : La Vie Athoise n°182.',
                '/images/events/biere.jpg',
                false,
                'https://oberbayern.fanfare-ormeignies.be',
            ],
            [
                'Ducasse des P’tits Urchons',
                'animation',
                'Site du Moulin, Irchonwelz',
                Carbon::create(2026, 10, 9, 19, 0),
                Carbon::create(2026, 10, 11, 18, 0),
                'Le géant P’tit Urchon est présent tout le week-end sous chapiteau : concert, manille, blind test, marche de 5 et 10 km, fanfares et course des P’tits Canards sur la Dendre. Source : La Vie Athoise n°182.',
                '/images/events/sortileges.jpg',
                false,
                null,
            ],
            [
                'Nuit de l’Obscurité à Maffle',
                'culture',
                'Maffle',
                Carbon::create(2026, 10, 10, 19, 0),
                Carbon::create(2026, 10, 10, 23, 0),
                'La Ville d’Ath et l’ASCEN proposent observation du ciel, expositions et promenade guidée à la rencontre des chauves-souris aux abords de la carrière de Maffle. Activités gratuites. Source : La Vie Athoise n°182.',
                '/images/events/ciel.jpg',
                false,
                'https://www.ath.be',
            ],
            [
                'Philathelia 2026',
                'foire',
                'Collège Saint-Julien, rue du Spectacle 1',
                Carbon::create(2026, 10, 11, 9, 0),
                Carbon::create(2026, 10, 11, 15, 30),
                '26e bourse philatélique du Cercle Royal Philatélique athois Les Amis du Timbre : timbres, cartes postales, monnaies, BD et livres régionaux. Entrée gratuite. Source : La Vie Athoise n°182.',
                '/images/events/foire.jpg',
                false,
                null,
            ],
            [
                'Les Chorales du Pays Vert fêtent leurs 50 ans',
                'culture',
                'Maison des Géants',
                Carbon::create(2026, 10, 11, 12, 15),
                Carbon::create(2026, 10, 11, 13, 30),
                'Concert « Le Chant des Fleurs » à 12 h 15 à la Maison des Géants, dans le cadre des Floralies. Entrée au tarif habituel du musée. Source : La Vie Athoise n°182.',
                '/images/events/concert.jpg',
                false,
                'https://maisondesgeants.be/les-floralies/',
            ],
            [
                'Harmonie Royale Union de Lorette — 175e anniversaire',
                'culture',
                'Quai de l’Entrepôt',
                Carbon::create(2026, 10, 16, 19, 0),
                Carbon::create(2026, 10, 18, 18, 0),
                'Week-end musical : concert Poulycroc le vendredi, concert de gala (Carmina Burana avec la Chorale Rencontre) le samedi, 47e Festival des fanfares de l’entité le dimanche dès 11 h 15, entrée gratuite. Source : La Vie Athoise n°182.',
                '/images/events/concert.jpg',
                false,
                null,
            ],
            [
                'Pickleball Days',
                'sport',
                'Hall CEVA',
                Carbon::create(2026, 10, 17, 10, 0),
                Carbon::create(2026, 10, 18, 13, 0),
                'Tournoi de pickleball en doubles le samedi 17 octobre de 10 h à 17 h (inscription avant le 1er octobre) et Discovery Day le dimanche 18 de 10 h à 13 h. Entrée libre. Source : La Vie Athoise n°182 / Ville d’Ath.',
                '/images/events/sport.jpg',
                false,
                'https://www.ath.be',
            ],
            [
                '1ère Foire aux Créateurs et produits du terroir',
                'foire',
                'Hall CEVA, chemin des Primevères',
                Carbon::create(2026, 10, 24, 11, 0),
                Carbon::create(2026, 10, 25, 18, 0),
                'Première foire aux créateurs et produits du terroir au hall CEVA, initiée par le club de balle pelote d’Oeudeghien. Créations artisanales, idées cadeaux et produits de bouche. Source : agenda de la Ville d’Ath.',
                '/images/events/foire.jpg',
                false,
                'https://www.ath.be/agenda/1ere-foire-aux-createurs-et-produits-du-terroir',
            ],
            [
                'Saint-Nicolas à l’Espace gallo-romain',
                'animation',
                'Espace gallo-romain',
                Carbon::create(2026, 11, 8, 10, 0),
                Carbon::create(2026, 11, 8, 18, 0),
                'Saint-Nicolas organisé avec Chez Pilates et Cotontige : jeux de société, gourmandises et découvertes antiques. De 10 h à 18 h, entrée 3 €. Source : La Vie Athoise n°182.',
                '/images/events/noel.jpg',
                false,
                null,
            ],
            [
                'Ath By Night',
                'animation',
                'Hall CEVA',
                Carbon::create(2026, 11, 14, 18, 0),
                Carbon::create(2026, 11, 15, 0, 0),
                'Concours Holstein & Jersey organisé par l’Association Wallonne de l’Élevage, de 18 h à minuit : showmanship, veau déguisé et soirée festive. Source : La Vie Athoise n°182.',
                '/images/events/sport.jpg',
                false,
                null,
            ],
            [
                'Marché de Noël des Heures Heureuses',
                'marche',
                'Ath',
                Carbon::create(2026, 11, 20, 15, 0),
                Carbon::create(2026, 11, 20, 22, 0),
                'L’ASBL Heures Heureuses organise son marché de Noël de 15 h à 22 h : articles cadeaux, décorations et idées pour préparer les fêtes, avec dégustation de saumon et champagne. Source : La Vie Athoise n°182.',
                '/images/events/noel.jpg',
                false,
                null,
            ],
            [
                'Semaine de l’Arbre — distribution de plants',
                'animation',
                'Marché aux Toiles',
                Carbon::create(2026, 11, 28, 10, 0),
                Carbon::create(2026, 11, 28, 12, 0),
                'Distribution de plants par la Ville d’Ath le samedi 28 novembre de 10 h à 12 h, dans le cadre de la Semaine de l’Arbre 2026, année des passereaux. Source : La Vie Athoise n°182.',
                '/images/events/floralies.jpg',
                false,
                'https://www.ath.be',
            ],
            [
                'Concert de Noël de la Chapelle Musicale de Bouvignies',
                'culture',
                'Église de la Sainte-Vierge, Bouvignies',
                Carbon::create(2026, 12, 12, 20, 0),
                Carbon::create(2026, 12, 12, 22, 0),
                'Concert de Noël le samedi 12 décembre à 20 h, avec la Royale Chorale Rencontre, Miyaghi Osada, Pascaline Flamme et le Quatuor de clarinettes Boréas. Source : La Vie Athoise n°182.',
                '/images/events/concert.jpg',
                false,
                null,
            ],
        ];
        $keepSlugs = [];
        foreach ($events as [$title, $type, $location, $starts, $ends, $desc, $cover, $featured, $url]) {
            $slug = Str::slug($title);
            $keepSlugs[] = $slug;
            Event::query()->updateOrCreate(['slug->fr' => $slug], [
                'title' => ['fr' => $title, 'nl' => $title, 'en' => $title],
                'slug' => ['fr' => $slug, 'nl' => $slug, 'en' => $slug],
                'description' => ['fr' => $desc, 'nl' => $desc, 'en' => $desc],
                'starts_at' => $starts,
                'ends_at' => $ends,
                'event_type_id' => $types[$type]->id,
                'location' => $location.', 7800 Ath',
                'status' => PublishStatus::Published,
                'cover_url' => $cover,
                'is_featured' => $featured,
                'external_url' => $url,
                'form_id' => null,
                'capacity' => null,
            ]);
        }
        Event::query()->whereNotIn('slug->fr', $keepSlugs)->delete();

        $basile = Merchant::query()->where('name', 'Le Comptoir de Basile')->first();
        $bella = Merchant::query()->where('name', 'Bella Donna')->first();
        $parfums = Merchant::query()->where('name', 'Paris Parfums')->first();
        foreach ([
            [$basile, 'Plateau découverte', 'Un plateau fromages + pain offert pour toute commande dès 25 €, jusqu’à dimanche.'],
            [$bella, 'Essayage privé', 'Un créneau cabine offert sur rendez-vous cette semaine.'],
            [$parfums, 'Échantillon cadeau', 'Un échantillon offert pour tout achat de parfum jusqu’à la fin du mois.'],
        ] as [$merchant, $title, $desc]) {
            if (! $merchant) {
                continue;
            }
            Deal::query()->updateOrCreate(
                ['merchant_id' => $merchant->id, 'title->fr' => $title],
                [
                    'title' => ['fr' => $title, 'nl' => $title, 'en' => $title],
                    'description' => ['fr' => $desc, 'nl' => $desc, 'en' => $desc],
                    'conditions' => ['fr' => 'Offre réservée aux stocks disponibles.', 'nl' => '', 'en' => ''],
                    'starts_on' => now()->subDay(),
                    'ends_on' => now()->addDays(10),
                    'status' => PublishStatus::Published,
                    'cover_url' => $merchant->cover_url,
                ]
            );
        }

        Post::query()->delete();
        foreach ([
            [
                'Ascension & Sortilèges : commerces ouverts',
                'city',
                false,
                'Le 14 mai, plusieurs membres de l’ACA ont ouvert pour l’Ascension et Sortilèges. Paris Parfums, Bella Donna, Yves Rocher, Le Comptoir de Basile et bien d’autres.',
                "À l’occasion de l’Ascension et de Sortilèges, l’ACA a publié la liste des commerces ouverts et fermés, telle que communiquée par les membres.\n\nParmi les vitrines ouvertes : Bubbles Jump, Paris Parfums, Tout Simplement Lui, Atome, Le Comptoir de Basile, Herboristerie Alara, La Pouponnière, Jolies Folies, Ikone, Ath’mosphère, Couleur Urbaine, Profil BD, Centre Équilibre, Frip’ Lover, PersonnalisATHion, Au Bon Coin, Bella Donna, Lusitanos et Yves Rocher Ath.\n\nEnsemble, faisons vivre le commerce local.",
            ],
            [
                'Gagnez une vidéo pour votre commerce',
                'members',
                false,
                'L’ACA cherche 20 nouveaux commerces pour ses capsules vidéo 2026. Adhésion 2026 payée, pas déjà filmé l’édition précédente, inscription jusqu’au 15 septembre auprès de secretaire@athinfo.be.',
                "L’Association des commerçants et artisans d’Ath lance un appel : 20 commerces membres peuvent gagner une capsule vidéo.\n\nConditions : être membre 2026 en ordre de cotisation, ne pas avoir déjà bénéficié de l’édition précédente, et s’inscrire avant le 15 septembre auprès de secretaire@athinfo.be.\n\nLes capsules sont destinées à faire rayonner les vitrines athoises sur les réseaux de l’ACA.",
            ],
            [
                'Bienvenue sur le nouvel annuaire',
                'association',
                false,
                'Fiches, carte, agenda et bons plans des membres de l’ACA, depuis 1911 au service du commerce athois.',
                "Le site de l’ACA rassemble enfin fiches, carte du centre-ville, agenda et bons plans au même endroit.\n\nL’association, fondée en 1911, a son siège rue Ernest Cambier 2 bte 1 à Ath. Contact : president@athinfo.be — +32 485 92 60 80.",
            ],
            [
                'L’ACA, depuis 1911',
                'association',
                false,
                'Commerçants et artisans d’Ath, rue Ernest Cambier, au rythme des géants et des vitrines.',
                "L’Association des commerçants et artisans d’Ath défend le commerce de proximité, anime le centre-ville et accueille chaque année la Ducasse.\n\nSiège : Rue Ernest Cambier 2 bte 1, 7800 Ath. Tél. +32 485 92 60 80. Facebook : Association des commerçants et artisans d’Ath.",
            ],
            [
                'Communiqué : agenda de septembre',
                'press',
                true,
                'Les dates à retenir pour la presse et les partenaires touristiques.',
                "Les dates à retenir pour la presse et les partenaires touristiques.\n\nContact presse : president@athinfo.be — Rue Ernest Cambier 2 bte 1, 7800 Ath.",
            ],
        ] as $i => [$title, $cat, $press, $excerpt, $body]) {
            $slug = Str::slug($title);
            Post::query()->updateOrCreate(['slug->fr' => $slug], [
                'title' => ['fr' => $title, 'nl' => $title, 'en' => $title],
                'slug' => ['fr' => $slug, 'nl' => $slug, 'en' => $slug],
                'excerpt' => ['fr' => $excerpt, 'nl' => $excerpt, 'en' => $excerpt],
                'body' => ['fr' => $body, 'nl' => $body, 'en' => $body],
                'category' => $cat,
                'published_at' => now()->subDays($i),
                'is_press_release' => $press,
                'cover_url' => str_contains($title, 'Sortilèges') ? '/images/events/sortileges.jpg' : $photos[($i + 2) % count($photos)],
            ]);
        }

        $this->cmsPage('association', 'L’association', "Fondée en 1911, l’Association des Commerçants et Artisans d’Ath défend le commerce de proximité, anime le centre-ville et accueille chaque année la Ducasse.\n\nSiège : Rue Ernest Cambier 2 bte 1, 7800 Ath.\nTéléphone : +32 485 92 60 80\nEmail : president@athinfo.be\nFacebook : Association des commerçants et artisans d’Ath\n\nLe comité gère l’annuaire, l’agenda et les adhésions. Les commerçants n’ont pas de compte : une suggestion de modification suffit pour tenir une fiche à jour.");
        $this->cmsPage('join', 'Devenir membre', "Adhérer à l’ACA, c’est une fiche dans l’annuaire, une voix dans les dossiers du centre-ville et l’accès aux animations collectives.\n\nLe comité valide chaque demande et crée ensuite la fiche.", [['type' => 'form', 'form_key' => 'join']]);
        $this->cmsPage('legal', 'Mentions légales', "Éditeur : Association des Commerçants et Artisans d’Ath\nSiège social : Rue Ernest Cambier 2 bte 1, 7800 Ath\nTéléphone : +32 485 92 60 80\nEmail : president@athinfo.be\n\nDirecteur de la publication : le président de l’ACA.\n\nPrestataire technique et réalisation du site : LD Media — Agence de communication à Ath et Chièvres — https://ldmedia.be — info@ldmedia.be\n\nHébergement : selon contrat d’hébergement en vigueur.\n\nLe contenu des fiches commerçants est fourni par les membres ou par suggestion. L’ACA s’efforce de le tenir à jour sans garantir l’exhaustivité des horaires et disponibilités.");
        $this->cmsPage('privacy', 'Vie privée', "Les formulaires collectent uniquement les données nécessaires au traitement de votre demande (contact, adhésion, suggestion de fiche, inscription). Les adresses IP sont hachées. Les messages sont purgés après 24 mois.\n\nResponsable de traitement : Association des Commerçants et Artisans d’Ath, Rue Ernest Cambier 2 bte 1, 7800 Ath — president@athinfo.be.\n\nVous pouvez demander l’accès, la rectification ou l’effacement de vos données à cette même adresse.");
        $this->cmsPage('cookies', 'Cookies', "Une bannière vous informe dès la première visite. Le site n’utilise pas de traceur publicitaire. Un cookie de session mémorise la langue choisie et, si vous acceptez, un cookie local retient votre choix sur la bannière.\n\nAucun outil d’audience tiers n’est installé par défaut. Pour toute question : president@athinfo.be.");
        $this->cmsPage('propose-event', 'Proposer un événement', 'Membres et partenaires peuvent proposer une date. Le comité publie après relecture.', [['type' => 'form', 'form_key' => 'event']]);
        $this->cmsPage('propose-deal', 'Proposer un bon plan', 'Une promo, un menu, un geste commercial : soumettez-le, l’ACA le met en ligne pour la durée indiquée.', [['type' => 'form', 'form_key' => 'deal']]);
    }

    private function form(string $key, string $name, array $fields): Form
    {
        $form = Form::query()->updateOrCreate(['system_key' => $key], [
            'name' => $name,
            'title' => ['fr' => $name, 'nl' => $name, 'en' => $name],
            'success_message' => ['fr' => 'Merci, votre message a bien été envoyé.', 'nl' => 'Dank u, we hebben uw bericht ontvangen.', 'en' => 'Thank you, your message has been sent.'],
            'ack_enabled' => true,
            'ack_subject' => ['fr' => 'Nous avons bien reçu votre message', 'nl' => 'We hebben uw bericht ontvangen', 'en' => 'We received your message'],
            'ack_body' => ['fr' => 'L’équipe de l’ACA revient vers vous rapidement.', 'nl' => 'Het ACA-team neemt spoedig contact op.', 'en' => 'The ACA team will get back to you shortly.'],
            'is_active' => true,
        ]);
        $form->fields()->delete();
        foreach ($fields as $i => $field) {
            [$type, $label, $required, $options, $width] = array_pad($field, 5, null);
            $form->fields()->create([
                'type' => $type,
                'label' => ['fr' => $label, 'nl' => $label, 'en' => $label],
                'options' => $options ? ['fr' => $options, 'nl' => $options, 'en' => $options] : null,
                'required' => $required,
                'width' => $width ?: 'full',
                'position' => $i,
            ]);
        }

        return $form;
    }

    private function cmsPage(string $routeKey, string $title, string $body, array $extra = []): void
    {
        $blocks = array_merge([['type' => 'text', 'body' => $body]], $extra);
        $slugs = config("aca.routes.{$routeKey}");
        Page::query()->updateOrCreate(
            ['slug->fr' => $slugs['fr']],
            [
                'title' => ['fr' => $title, 'nl' => $title, 'en' => $title],
                'slug' => $slugs,
                'blocks' => ['fr' => $blocks, 'nl' => $blocks, 'en' => $blocks],
                'is_published' => true,
            ]
        );
    }
}
