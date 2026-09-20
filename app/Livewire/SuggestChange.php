<?php

namespace App\Livewire;

use App\Models\Merchant;
use App\Services\SuggestionApplier;
use Livewire\Component;

class SuggestChange extends Component
{
    public Merchant $merchant;

    public string $sender_name = '';

    public string $sender_email = '';

    public string $sender_role = 'manager';

    public string $name = '';

    public string $address = '';

    public string $phone = '';

    public string $email = '';

    public string $website = '';

    public string $description = '';

    public bool $consent = false;

    public bool $sent = false;

    public function mount(Merchant $merchant): void
    {
        $this->merchant = $merchant;
        $this->name = $merchant->name;
        $this->address = (string) $merchant->address;
        $this->phone = (string) $merchant->phone;
        $this->email = (string) $merchant->email;
        $this->website = (string) $merchant->website;
        $this->description = $merchant->t('description', 'fr');
    }

    public function submit(SuggestionApplier $applier): void
    {
        $this->validate([
            'sender_name' => 'required|string|max:120',
            'sender_email' => 'required|email',
            'sender_role' => 'required|in:manager,employee,visitor',
            'consent' => 'accepted',
        ]);

        [$original, $changed] = $applier->diff($this->merchant, [
            'name' => $this->name,
            'address' => $this->address,
            'phone' => $this->phone,
            'email' => $this->email,
            'website' => $this->website,
            'description' => ['fr' => $this->description],
        ]);

        if ($changed === []) {
            $this->addError('name', __('Aucun champ n’a été modifié.'));

            return;
        }

        $this->merchant->suggestions()->create([
            'sender_name' => $this->sender_name,
            'sender_email' => $this->sender_email,
            'sender_role' => $this->sender_role,
            'payload' => $changed,
            'original' => $original,
            'status' => 'pending',
        ]);

        $this->sent = true;
    }

    public function render()
    {
        return view('livewire.suggest-change');
    }
}
