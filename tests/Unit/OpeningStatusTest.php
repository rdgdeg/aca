<?php

namespace Tests\Unit;

use App\Models\Merchant;
use App\Models\OpeningHour;
use App\Services\OpeningStatus;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OpeningStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_open_and_closing_soon(): void
    {
        $merchant = Merchant::query()->create([
            'name' => 'Test',
            'slug' => ['fr' => 'test'],
            'status' => 'published',
        ]);
        OpeningHour::query()->create([
            'merchant_id' => $merchant->id,
            'weekday' => 3,
            'opens_at' => '09:00',
            'closes_at' => '18:00',
        ]);
        $merchant->load('openingHours', 'closures');

        $service = new OpeningStatus;
        $open = $service->for($merchant, Carbon::parse('2026-09-16 10:00', 'Europe/Brussels'));
        $this->assertTrue($open['open']);
        $this->assertSame('open', $open['key']);

        $soon = $service->for($merchant, Carbon::parse('2026-09-16 17:45', 'Europe/Brussels'));
        $this->assertTrue($soon['open']);
        $this->assertSame('closing_soon', $soon['key']);

        $closed = $service->for($merchant, Carbon::parse('2026-09-16 20:00', 'Europe/Brussels'));
        $this->assertFalse($closed['open']);
    }
}
