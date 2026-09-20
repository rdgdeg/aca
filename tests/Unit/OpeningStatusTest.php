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

    public function test_holiday_closed_merchant_is_closed_on_easter_monday(): void
    {
        $merchant = Merchant::query()->create([
            'name' => 'Férié',
            'slug' => ['fr' => 'ferie'],
            'status' => 'published',
            'holiday_closed' => true,
        ]);
        OpeningHour::query()->create([
            'merchant_id' => $merchant->id,
            'weekday' => 1,
            'opens_at' => '09:00',
            'closes_at' => '18:00',
        ]);
        $merchant->load('openingHours', 'closures');

        $service = new OpeningStatus;
        $holiday = $service->for($merchant, Carbon::parse('2026-04-06 11:00', 'Europe/Brussels'));

        $this->assertFalse($holiday['open']);
        $this->assertSame('holiday', $holiday['key']);
    }
}
