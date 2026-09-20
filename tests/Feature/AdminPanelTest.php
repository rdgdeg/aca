<?php

namespace Tests\Feature;

use App\Filament\Resources\EventResource;
use App\Filament\Resources\EventResource\Pages\EditEvent;
use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_admin_stays_in_french_when_the_public_locale_is_english(): void
    {
        $user = User::query()->where('email', 'info@ldmedia.be')->first();
        $this->assertNotNull($user);

        $this->actingAs($user)
            ->withSession(['locale' => 'en'])
            ->get('/admin')
            ->assertOk()
            ->assertSee('Tableau de bord')
            ->assertSee('Événements')
            ->assertSee('Demandes')
            ->assertDontSee('Toutes Les Demandes')
            ->assertSee('lang="fr"', false);
    }

    public function test_merchants_index_renders_in_french(): void
    {
        $user = User::query()->where('email', 'info@ldmedia.be')->first();
        $this->assertNotNull($user);

        $this->actingAs($user)
            ->get('/admin/merchants')
            ->assertOk()
            ->assertSee('Commerçants')
            ->assertSee('Nouveau commerçant');
    }

    public function test_event_edit_page_renders_in_french(): void
    {
        $user = User::query()->where('email', 'info@ldmedia.be')->first();
        $event = Event::query()->first();
        $this->assertNotNull($user);
        $this->assertNotNull($event);

        $this->actingAs($user)
            ->get('/admin/events/'.$event->id.'/edit')
            ->assertOk()
            ->assertSee('Sauvegarder les modifications')
            ->assertSee('Quand et où')
            ->assertSee('Aller au contenu')
            ->assertSee('Affiche actuelle')
            ->assertDontSee('Save changes')
            ->assertDontSee('filament-panels::layout.skip_to_content.label');
    }

    public function test_event_update_keeps_cover_and_saves_french_title(): void
    {
        $user = User::query()->where('email', 'info@ldmedia.be')->first();
        $event = Event::query()->whereNotNull('cover_url')->first();
        $this->assertNotNull($user);
        $this->assertNotNull($event);

        $cover = $event->cover_url;

        $this->actingAs($user);

        Livewire::test(EditEvent::class, ['record' => $event->getKey()])
            ->fillForm([
                'title' => [
                    'fr' => 'Titre modifié pour test',
                    'nl' => $event->getTranslation('title', 'nl'),
                    'en' => $event->getTranslation('title', 'en'),
                ],
                'starts_at' => $event->starts_at,
                'status' => $event->status->value,
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $event->refresh();

        $this->assertSame('Titre modifié pour test', $event->getTranslation('title', 'fr'));
        $this->assertSame($cover, $event->cover_url);
    }

    public function test_event_slug_is_filled_from_french_title(): void
    {
        $data = EventResource::fillSlug([
            'title' => ['fr' => 'Fête à la Grand-Place'],
            'slug' => ['fr' => null, 'nl' => null, 'en' => null],
        ]);

        $this->assertSame('fete-a-la-grand-place', $data['slug']['fr']);
    }
}
