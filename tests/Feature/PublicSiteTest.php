<?php

namespace Tests\Feature;

use App\Livewire\MerchantDirectory;
use App\Models\Event;
use App\Models\Form;
use App\Models\MembershipApplication;
use App\Models\Merchant;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PublicSiteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_localized_directory_urls(): void
    {
        $this->get('/fr/commercants')
            ->assertOk()
            ->assertSee('Les membres de l’ACA')
            ->assertSee('Bella Donna')
            ->assertSee('aca-map')
            ->assertSee('lg:grid-cols-4', false);
        $this->get('/nl/handelaars')->assertOk()->assertSee('Handelaars');
        $this->get('/en/shops')->assertOk()->assertSee('Shops');
    }

    public function test_directory_paginates_members(): void
    {
        Livewire::test(MerchantDirectory::class)
            ->assertSee('Bella Donna')
            ->assertSeeHtml('lg:grid-cols-4')
            ->assertDontSee('Showing')
            ->assertSee('1 à 12 sur')
            ->call('gotoPage', 2)
            ->assertOk()
            ->assertSee('13 à')
            ->assertDontSee('Showing');
    }

    public function test_merchant_sheet_and_map(): void
    {
        $this->get('/fr/commerce/bella-donna')->assertOk()
            ->assertSee('Bella Donna')
            ->assertSee('Contact')
            ->assertSee('Horaires')
            ->assertSee('Suggérer une modification')
            ->assertSee('schema.org')
            ->assertSee('LocalBusiness');
        $this->get('/fr/carte')->assertRedirect();
    }

    public function test_language_switch_maps_slugs(): void
    {
        $this->get('/fr/commercants')->assertOk()->assertSee('/nl/handelaars');
        $this->get('/fr/commerce/bella-donna')->assertOk()->assertSee('/nl/zaak/bella-donna');
    }

    public function test_contact_form_creates_submission(): void
    {
        $form = Form::system('contact');
        $this->assertNotNull($form);

        $payload = [
            'consent' => true,
            'website' => '',
            'fields' => [],
        ];
        foreach ($form->fields as $field) {
            $payload['fields'][$field->id] = match ($field->type) {
                'email' => 'visiteur@example.com',
                'checkbox' => ['oui'],
                'select' => $field->optionList()[0] ?? 'Question',
                default => 'Bonjour ACA',
            };
        }

        $this->post('/fr/formulaires/'.$form->id, $payload)->assertRedirect();
        $this->assertDatabaseCount('submissions', 1);

        $submission = Submission::query()->first();
        $this->assertSame('visiteur@example.com', $submission?->senderEmail());
        $this->assertSame('Question', $submission?->data['Raison du contact'] ?? null);
    }

    public function test_legal_pages_and_hreflang(): void
    {
        $this->get('/fr/vie-privee')->assertOk();
        $this->get('/fr/cookies')->assertOk()->assertSee('cookie', false);
        $this->get('/fr/mentions-legales')->assertOk()->assertSee('Mentions légales')->assertSee('LD Media');
        $home = $this->get('/fr');
        $home->assertOk()->assertSee('hreflang', false)->assertSee('schema.org')->assertSee('LD Media')
            ->assertSee('Accueil')->assertSee('Membres de l’ACA')->assertSee('Raison sociale');
    }

    public function test_featured_merchant_exists(): void
    {
        $this->assertGreaterThanOrEqual(20, Merchant::published()->count());
        $this->assertDatabaseHas('merchants', ['name' => 'Le Comptoir de Basile']);
        $this->assertDatabaseHas('merchants', ['name' => 'Profil BD']);
    }

    public function test_join_form_creates_membership_application(): void
    {
        $form = Form::system('join');
        $this->assertNotNull($form);

        $payload = [
            'consent' => true,
            'website' => '',
            'fields' => [],
        ];
        foreach ($form->fields as $field) {
            $payload['fields'][$field->id] = match ($field->type) {
                'email' => 'membre@example.com',
                'select' => 'Mode',
                'checkbox' => $field->optionList() !== [] ? [$field->optionList()[0]] : ['oui'],
                default => str_contains(mb_strtolower($field->t('label')), 'postal') ? '7800' : 'Boutique Test',
            };
        }

        $this->post('/fr/formulaires/'.$form->id, $payload)->assertRedirect();
        $this->assertDatabaseCount('submissions', 1);
        $this->assertDatabaseHas('membership_applications', ['company_name' => 'Boutique Test']);
        $this->assertNotNull(MembershipApplication::query()->first());
    }

    public function test_join_form_requires_a_category(): void
    {
        $form = Form::system('join');
        $this->assertNotNull($form);

        $payload = [
            'consent' => true,
            'website' => '',
            'fields' => [],
        ];
        foreach ($form->fields as $field) {
            $payload['fields'][$field->id] = match ($field->type) {
                'email' => 'membre@example.com',
                'checkbox' => [],
                default => str_contains(mb_strtolower($field->t('label')), 'autre') ? '' : 'Boutique Test',
            };
        }

        $this->from('/fr/devenir-membre')
            ->post('/fr/formulaires/'.$form->id, $payload)
            ->assertRedirect('/fr/devenir-membre')
            ->assertSessionHasErrors();
    }

    public function test_admin_can_open_forms_and_all_submissions(): void
    {
        $user = User::query()->where('email', 'info@ldmedia.be')->first();
        $form = Form::system('join');
        $this->assertNotNull($user);
        $this->assertNotNull($form);

        $this->actingAs($user)
            ->get('/admin/forms')
            ->assertOk()
            ->assertSee('Devenir membre')
            ->assertSee('Toutes les demandes');

        $this->actingAs($user)
            ->get('/admin/forms/'.$form->id)
            ->assertOk()
            ->assertSee('Demandes reçues');

        $this->actingAs($user)
            ->get('/admin/submissions')
            ->assertOk();
    }

    public function test_agenda_puts_calendar_first_with_real_events(): void
    {
        $html = $this->get('/fr/agenda')->assertOk()->getContent();
        $calendar = strpos($html, 'grid-cols-7');
        $listing = strpos($html, 'md:grid-cols-2 lg:grid-cols-3');
        $this->assertNotFalse($calendar);
        $this->assertNotFalse($listing);
        $this->assertLessThan($listing, $calendar);
        $this->assertStringContainsString('agenda-calendar hidden', $html);
        $this->assertStringContainsString('md:grid', $html);

        $this->get('/fr/agenda')
            ->assertSee('Floralies')
            ->assertSee('Gouyasse')
            ->assertDontSee('Braderie de printemps')
            ->assertDontSee('Nuit des caves')
            ->assertDontSee('Soldes d’été');
    }

    public function test_home_featured_photo_join_layout_and_header(): void
    {
        $response = $this->get('/fr')->assertOk();
        $html = $response->getContent();
        $header = substr($html, 0, (int) strpos($html, '</header>'));

        $response
            ->assertSee('Floralies')
            ->assertSee('Une fiche dans l’annuaire')
            ->assertSee('floralies.jpg')
            ->assertSee('lg:grid-cols-4', false);
        $this->assertStringNotContainsString('Que cherchez-vous', $header);
        $this->assertStringNotContainsString('Bons plans', $header);
        $this->assertStringContainsString('footer class="bg-plum text-white"', $html);
    }

    public function test_featured_event_is_spotlight_otherwise_nearest(): void
    {
        $spotlight = Event::spotlight();
        $this->assertNotNull($spotlight);
        $this->assertTrue($spotlight->is_featured);
        $this->assertStringContainsString('Floralies', (string) $spotlight->t('title', 'fr'));

        Event::query()->update(['is_featured' => false]);
        $fallback = Event::spotlight();
        $this->assertNotNull($fallback);
        $this->assertFalse((bool) $fallback->is_featured);
        $this->assertEquals(Event::upcoming()->first()?->id, $fallback->id);
    }

    public function test_home_agenda_banner_countdown_and_newcomers(): void
    {
        $this->get('/fr')
            ->assertOk()
            ->assertSee('agenda-arrow', false)
            ->assertDontSee('agenda-tray', false)
            ->assertSee('Week-end à Ath')
            ->assertSee('Parking & accès')
            ->assertDontSee('Parcours shopping')
            ->assertDontSee('Ils viennent d’arriver')
            ->assertDontSee('120 €')
            ->assertSee('Lire la fiche')
            ->assertSee('Contact')
            ->assertSee('Rejoindre l’association');
    }

    public function test_directory_offers_load_more_near_me_and_favorites(): void
    {
        $merchant = Merchant::published()->first();
        $this->assertNotNull($merchant);

        Livewire::test(MerchantDirectory::class)
            ->assertSee('Charger plus')
            ->assertSee('Près de moi')
            ->assertSee('Mes vitrines')
            ->call('loadMore')
            ->assertSet('perPage', 24)
            ->call('showFavorites', [$merchant->id])
            ->assertSet('favoritesOnly', true)
            ->assertSee($merchant->name)
            ->call('setLocation', 50.6305, 3.778)
            ->assertSet('sort', 'near')
            ->assertSee('Près de moi');
    }

    public function test_weekend_trails_and_parking_pages_render(): void
    {
        $this->get('/fr/week-end')->assertOk()->assertSee('Week-end à Ath')->assertSee('Vitrines ouvertes');
        $this->get('/nl/weekend')->assertOk();
        $this->get('/fr/parcours')->assertRedirect();
        $this->get('/fr/parking')->assertOk()->assertSee('Boulevard de Mons')->assertSee('Itinéraire');
        $this->get('/nl/parkeren')->assertOk();
    }

    public function test_contact_page_replaces_association_and_shows_committee(): void
    {
        $this->get('/fr/contact')
            ->assertOk()
            ->assertSee('Le comité')
            ->assertSee('president@athinfo.be')
            ->assertSee('Depuis 1911')
            ->assertSee('Écrire au comité')
            ->assertSee('Raison du contact')
            ->assertDontSee('info@ldmedia.be')
            ->assertDontSee('0476 32 32 75');
        $this->get('/fr/association')->assertRedirect();
        $this->get('/fr/bons-plans')->assertRedirect();
    }

    public function test_contact_form_requires_a_reason(): void
    {
        $form = Form::system('contact');
        $this->assertNotNull($form);
        $reason = $form->fields->first(fn ($field) => $field->t('label') === 'Raison du contact');
        $this->assertNotNull($reason);

        $payload = [
            'consent' => true,
            'website' => '',
            'fields' => [],
        ];
        foreach ($form->fields as $field) {
            $payload['fields'][$field->id] = match ($field->type) {
                'email' => 'visiteur@example.com',
                'select' => '',
                default => 'Bonjour ACA',
            };
        }

        $this->from('/fr/contact')
            ->post('/fr/formulaires/'.$form->id, $payload)
            ->assertRedirect('/fr/contact')
            ->assertSessionHasErrors('fields.'.$reason->id);
        $this->assertDatabaseCount('submissions', 0);
    }

    public function test_header_footer_show_facebook_and_city_partner(): void
    {
        $this->get('/fr')
            ->assertOk()
            ->assertSee('header-facebook hidden lg:inline-flex', false)
            ->assertSee('aria-label="Facebook"', false)
            ->assertSee('facebook.com/aca.commercantsdath', false)
            ->assertSee('Partenaires')
            ->assertSee('Ville d’Ath')
            ->assertSee('https://www.ath.be', false)
            ->assertSee('Retour en haut')
            ->assertSee('Administration')
            ->assertSee('/admin', false)
            ->assertSee('Agence de communication')
            ->assertSee('text-center md:grid-cols-4 md:text-left', false);
    }

    public function test_public_layout_loads_the_montserrat_webfont(): void
    {
        $html = $this->get('/fr')->assertOk()->getContent();

        $this->assertStringContainsString('@font-face', $html);
        $this->assertStringContainsString('font-family: "Montserrat"', $html);
        $this->assertStringContainsString('type="font/woff2"', $html);
        $this->assertStringContainsString('as="font"', $html);
    }

    public function test_home_search_keeps_the_submit_label_inside_the_bar(): void
    {
        $this->get('/fr')
            ->assertOk()
            ->assertSee('search-bar mx-auto mt-8 flex w-full min-w-0', false)
            ->assertSee('Rechercher');
    }

    public function test_agenda_filters_months_and_paginates(): void
    {
        $this->get('/fr/agenda')
            ->assertOk()
            ->assertSee('Tous les mois')
            ->assertSee('Floralies')
            ->assertSee('1 à 6 sur');

        $this->get('/fr/agenda?month=2026-10')
            ->assertOk()
            ->assertSee('Floralies')
            ->assertSee('Foire aux Créateurs')
            ->assertSee('octobre 2026');
    }

    public function test_merchant_sheet_uses_one_tap_directions(): void
    {
        $merchant = Merchant::published()->whereNotNull('address')->orderBy('name')->first();
        $this->assertNotNull($merchant);

        $this->get('/fr/commerce/'.$merchant->t('slug'))
            ->assertOk()
            ->assertSee('Voir sur Google Maps')
            ->assertSee('google.com/maps/dir', false)
            ->assertSee('Fiche précédente')
            ->assertSee('Fiche suivante');
    }
}
