<?php

use App\Http\Controllers\SiteController;
use App\Http\Middleware\DetectLocale;
use App\Http\Middleware\SetLocale;
use App\Livewire\MerchantDirectory;
use Illuminate\Support\Facades\Route;

Route::middleware(DetectLocale::class)->group(function () {
    Route::get('/', fn () => redirect('/fr'));
});

Route::prefix('{locale}')
    ->whereIn('locale', ['fr', 'nl', 'en'])
    ->middleware(SetLocale::class)
    ->group(function () {
        Route::get('/', [SiteController::class, 'home'])->name('home');
        Route::get('/commercants', MerchantDirectory::class)->name('merchants.fr');
        Route::get('/handelaars', MerchantDirectory::class)->name('merchants.nl');
        Route::get('/shops', MerchantDirectory::class)->name('merchants.en');
        Route::get('/commercants/{category}', MerchantDirectory::class)->name('merchants.category.fr');
        Route::get('/handelaars/{category}', MerchantDirectory::class)->name('merchants.category.nl');
        Route::get('/shops/{category}', MerchantDirectory::class)->name('merchants.category.en');
        Route::get('/commerce/{slug}', [SiteController::class, 'merchant'])->name('merchant.fr');
        Route::get('/zaak/{slug}', [SiteController::class, 'merchant'])->name('merchant.nl');
        Route::get('/shop/{slug}', [SiteController::class, 'merchant'])->name('merchant.en');
        Route::get('/carte', [SiteController::class, 'map'])->name('map.fr');
        Route::get('/kaart', [SiteController::class, 'map'])->name('map.nl');
        Route::get('/map', [SiteController::class, 'map'])->name('map.en');
        Route::get('/agenda', [SiteController::class, 'events'])->name('events.fr');
        Route::get('/events', [SiteController::class, 'events'])->name('events.en');
        Route::get('/agenda/{slug}.ics', [SiteController::class, 'eventIcs']);
        Route::get('/events/{slug}.ics', [SiteController::class, 'eventIcs']);
        Route::get('/agenda/{slug}', [SiteController::class, 'event'])->name('event.fr');
        Route::get('/events/{slug}', [SiteController::class, 'event'])->name('event.en');
        Route::get('/bons-plans', [SiteController::class, 'deals'])->name('deals.fr');
        Route::get('/voordelen', [SiteController::class, 'deals'])->name('deals.nl');
        Route::get('/deals', [SiteController::class, 'deals'])->name('deals.en');
        Route::get('/actualites', [SiteController::class, 'news'])->name('news.fr');
        Route::get('/nieuws', [SiteController::class, 'news'])->name('news.nl');
        Route::get('/news', [SiteController::class, 'news'])->name('news.en');
        Route::get('/actualites/{slug}', [SiteController::class, 'post'])->name('post.fr');
        Route::get('/nieuws/{slug}', [SiteController::class, 'post'])->name('post.nl');
        Route::get('/news/{slug}', [SiteController::class, 'post'])->name('post.en');
        Route::get('/presse', [SiteController::class, 'press'])->name('press.fr');
        Route::get('/pers', [SiteController::class, 'press'])->name('press.nl');
        Route::get('/press', [SiteController::class, 'press'])->name('press.en');
        Route::get('/association', fn () => redirect()->to(aca_url('contact')))->name('association.fr');
        Route::get('/vereniging', fn () => redirect()->to(aca_url('contact')))->name('association.nl');
        Route::get('/devenir-membre', [SiteController::class, 'pageByKey'])->defaults('key', 'join')->name('join.fr');
        Route::get('/lid-worden', [SiteController::class, 'pageByKey'])->defaults('key', 'join')->name('join.nl');
        Route::get('/join', [SiteController::class, 'pageByKey'])->defaults('key', 'join')->name('join.en');
        Route::get('/contact', [SiteController::class, 'contact'])->name('contact');
        Route::get('/mentions-legales', [SiteController::class, 'pageByKey'])->defaults('key', 'legal');
        Route::get('/wettelijke-vermeldingen', [SiteController::class, 'pageByKey'])->defaults('key', 'legal');
        Route::get('/legal-notice', [SiteController::class, 'pageByKey'])->defaults('key', 'legal');
        Route::get('/vie-privee', [SiteController::class, 'pageByKey'])->defaults('key', 'privacy');
        Route::get('/privacy', [SiteController::class, 'pageByKey'])->defaults('key', 'privacy');
        Route::get('/cookies', [SiteController::class, 'pageByKey'])->defaults('key', 'cookies');
        Route::get('/proposer-evenement', [SiteController::class, 'pageByKey'])->defaults('key', 'propose-event');
        Route::get('/evenement-voorstellen', [SiteController::class, 'pageByKey'])->defaults('key', 'propose-event');
        Route::get('/propose-event', [SiteController::class, 'pageByKey'])->defaults('key', 'propose-event');
        Route::get('/proposer-bon-plan', [SiteController::class, 'pageByKey'])->defaults('key', 'propose-deal');
        Route::get('/voordeel-voorstellen', [SiteController::class, 'pageByKey'])->defaults('key', 'propose-deal');
        Route::get('/propose-deal', [SiteController::class, 'pageByKey'])->defaults('key', 'propose-deal');
        Route::get('/week-end', [SiteController::class, 'weekend'])->name('weekend.fr');
        Route::get('/weekend', [SiteController::class, 'weekend'])->name('weekend.nl');
        Route::get('/parcours', fn () => redirect()->to(aca_url('merchants')))->name('trails.fr');
        Route::get('/routes', fn () => redirect()->to(aca_url('merchants')))->name('trails.nl');
        Route::get('/trails', fn () => redirect()->to(aca_url('merchants')))->name('trails.en');
        Route::get('/parking', [SiteController::class, 'parking'])->name('parking.fr');
        Route::get('/parkeren', [SiteController::class, 'parking'])->name('parking.nl');
        Route::post('/formulaires/{form}', [SiteController::class, 'submitForm'])->name('forms.submit');
        Route::get('/{slug}', [SiteController::class, 'cms'])->where('slug', '[A-Za-z0-9\-]+');
    });

Route::get('/calendar.ics', [SiteController::class, 'calendarFeed']);
