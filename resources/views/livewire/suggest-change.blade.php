<div>
    @if ($sent)
        <p class="rounded-2xl bg-mist p-6">{{ __('Merci. L’équipe ACA relira votre suggestion et mettra la fiche à jour si besoin.') }}</p>
    @else
        <form wire:submit="submit" class="space-y-4">
            <div class="grid md:grid-cols-2 gap-4">
                <label class="block text-sm">{{ __('Votre nom') }}
                    <input wire:model="sender_name" class="mt-1 w-full rounded-xl border border-plum/20 p-3">
                </label>
                <label class="block text-sm">{{ __('Votre email') }}
                    <input type="email" wire:model="sender_email" class="mt-1 w-full rounded-xl border border-plum/20 p-3">
                </label>
            </div>
            <label class="block text-sm">{{ __('Vous êtes') }}
                <select wire:model="sender_role" class="mt-1 w-full rounded-xl border border-plum/20 p-3">
                    <option value="manager">{{ __('Gérant') }}</option>
                    <option value="employee">{{ __('Employé') }}</option>
                    <option value="visitor">{{ __('Visiteur') }}</option>
                </select>
            </label>
            <label class="block text-sm">{{ __('Nom du commerce') }}
                <input wire:model="name" class="mt-1 w-full rounded-xl border border-plum/20 p-3">
            </label>
            <label class="block text-sm">{{ __('Adresse') }}
                <input wire:model="address" class="mt-1 w-full rounded-xl border border-plum/20 p-3">
            </label>
            <div class="grid md:grid-cols-2 gap-4">
                <label class="block text-sm">{{ __('Téléphone') }}
                    <input wire:model="phone" class="mt-1 w-full rounded-xl border border-plum/20 p-3">
                </label>
                <label class="block text-sm">{{ __('Email du commerce') }}
                    <input wire:model="email" class="mt-1 w-full rounded-xl border border-plum/20 p-3">
                </label>
            </div>
            <label class="block text-sm">{{ __('Site web') }}
                <input wire:model="website" class="mt-1 w-full rounded-xl border border-plum/20 p-3">
            </label>
            <label class="block text-sm">{{ __('Description') }}
                <textarea wire:model="description" rows="5" class="mt-1 w-full rounded-xl border border-plum/20 p-3"></textarea>
            </label>
            <label class="flex items-start gap-2 text-sm">
                <input type="checkbox" wire:model="consent" class="mt-1">
                <span>{{ __('J’accepte le traitement de cette demande.') }}</span>
            </label>
            @error('sender_name') <p class="text-blush text-sm">{{ $message }}</p> @enderror
            @error('consent') <p class="text-blush text-sm">{{ $message }}</p> @enderror
            @error('name') <p class="text-blush text-sm">{{ $message }}</p> @enderror
            <button class="btn-plum" type="submit">{{ __('Envoyer la suggestion') }}</button>
        </form>
    @endif
</div>
