<?php

return [
    'locales' => [
        'fr' => ['name' => 'Français', 'short' => 'FR'],
        'nl' => ['name' => 'Nederlands', 'short' => 'NL'],
        'en' => ['name' => 'English', 'short' => 'EN'],
    ],
    'fallback_locale' => 'fr',
    'routes' => [
        'home' => ['fr' => '/', 'nl' => '/', 'en' => '/'],
        'merchants' => ['fr' => 'commercants', 'nl' => 'handelaars', 'en' => 'shops'],
        'merchant' => ['fr' => 'commerce', 'nl' => 'zaak', 'en' => 'shop'],
        'map' => ['fr' => 'carte', 'nl' => 'kaart', 'en' => 'map'],
        'events' => ['fr' => 'agenda', 'nl' => 'agenda', 'en' => 'events'],
        'event' => ['fr' => 'agenda', 'nl' => 'agenda', 'en' => 'events'],
        'deals' => ['fr' => 'bons-plans', 'nl' => 'voordelen', 'en' => 'deals'],
        'news' => ['fr' => 'actualites', 'nl' => 'nieuws', 'en' => 'news'],
        'post' => ['fr' => 'actualites', 'nl' => 'nieuws', 'en' => 'news'],
        'press' => ['fr' => 'presse', 'nl' => 'pers', 'en' => 'press'],
        'association' => ['fr' => 'association', 'nl' => 'vereniging', 'en' => 'association'],
        'join' => ['fr' => 'devenir-membre', 'nl' => 'lid-worden', 'en' => 'join'],
        'contact' => ['fr' => 'contact', 'nl' => 'contact', 'en' => 'contact'],
        'legal' => ['fr' => 'mentions-legales', 'nl' => 'wettelijke-vermeldingen', 'en' => 'legal-notice'],
        'privacy' => ['fr' => 'vie-privee', 'nl' => 'privacy', 'en' => 'privacy'],
        'cookies' => ['fr' => 'cookies', 'nl' => 'cookies', 'en' => 'cookies'],
        'propose-event' => ['fr' => 'proposer-evenement', 'nl' => 'evenement-voorstellen', 'en' => 'propose-event'],
        'propose-deal' => ['fr' => 'proposer-bon-plan', 'nl' => 'voordeel-voorstellen', 'en' => 'propose-deal'],
        'weekend' => ['fr' => 'week-end', 'nl' => 'weekend', 'en' => 'weekend'],
        'trails' => ['fr' => 'parcours', 'nl' => 'routes', 'en' => 'trails'],
        'parking' => ['fr' => 'parking', 'nl' => 'parkeren', 'en' => 'parking'],
    ],
    'partners' => [
        [
            'name' => 'Ville d’Ath',
            'url' => 'https://www.ath.be',
        ],
        [
            'name' => 'Pays des Collines',
            'url' => 'https://www.paysdescollines.be',
        ],
    ],
    'category_slugs' => [
        'mode' => ['fr' => 'mode', 'nl' => 'mode', 'en' => 'fashion'],
        'alimentation' => ['fr' => 'alimentation', 'nl' => 'voeding', 'en' => 'food'],
        'horeca' => ['fr' => 'horeca', 'nl' => 'horeca', 'en' => 'horeca'],
        'beaute' => ['fr' => 'beaute-bien-etre', 'nl' => 'schoonheid-welzijn', 'en' => 'beauty-wellness'],
        'maison' => ['fr' => 'maison-deco', 'nl' => 'wonen-deco', 'en' => 'home-decor'],
        'services' => ['fr' => 'services', 'nl' => 'diensten', 'en' => 'services'],
        'culture' => ['fr' => 'culture-loisirs', 'nl' => 'cultuur-vrije-tijd', 'en' => 'culture-leisure'],
    ],
];
