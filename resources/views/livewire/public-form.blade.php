<div class="h-full">
    @if ($sent)
        <p class="rounded-2xl bg-mist p-6 sm:col-span-2">{{ $success }}</p>
    @else
        <form wire:submit="submit" class="grid gap-4 sm:grid-cols-2">
            <input type="text" wire:model="website" class="hidden" tabindex="-1" autocomplete="off">
            @foreach ($form->fields as $field)
                @if (in_array($field->type, ['heading', 'help']))
                    <p class="pt-2 text-lg font-semibold sm:col-span-2">{{ $field->t('label') }}</p>
                    @if ($field->t('help'))<p class="text-sm text-ink/60 sm:col-span-2">{{ $field->t('help') }}</p>@endif
                    @continue
                @endif
                <label class="block {{ $field->width === 'half' ? '' : 'sm:col-span-2' }}">
                    <span class="text-sm">{{ $field->t('label') }} @if($field->required)*@endif</span>
                    @if ($field->type === 'textarea')
                        <textarea wire:model="fields.{{ $field->id }}" rows="4" class="mt-1 w-full rounded-xl border border-plum/25 bg-white p-3"></textarea>
                    @elseif ($field->type === 'select')
                        <select wire:model="fields.{{ $field->id }}" class="mt-1 w-full rounded-xl border border-plum/25 bg-white p-3">
                            <option value="">—</option>
                            @foreach ($field->optionList() as $opt)
                                <option value="{{ $opt }}">{{ $opt }}</option>
                            @endforeach
                        </select>
                    @elseif ($field->type === 'radio')
                        <div class="mt-2 space-y-1">
                            @foreach ($field->optionList() as $opt)
                                <label class="flex gap-2 items-center"><input type="radio" wire:model="fields.{{ $field->id }}" value="{{ $opt }}"> {{ $opt }}</label>
                            @endforeach
                        </div>
                    @elseif ($field->type === 'checkbox')
                        <div class="mt-2 flex flex-wrap gap-2">
                            @foreach ($field->optionList() as $opt)
                                <label class="inline-flex cursor-pointer items-center gap-2 rounded-full border border-plum/20 bg-white px-3 py-1.5 text-sm">
                                    <input type="checkbox" wire:model="fields.{{ $field->id }}" value="{{ $opt }}" class="accent-plum">
                                    {{ $opt }}
                                </label>
                            @endforeach
                        </div>
                    @else
                        <input type="{{ $field->type === 'email' ? 'email' : ($field->type === 'number' ? 'number' : ($field->type === 'date' ? 'date' : 'text')) }}"
                               wire:model="fields.{{ $field->id }}"
                               @if (str_contains(mb_strtolower($field->t('label')), 'téléphone') || str_contains(mb_strtolower($field->t('label')), 'tel')) inputmode="tel" @endif
                               class="mt-1 w-full rounded-xl border border-plum/25 bg-white p-3">
                    @endif
                    @error('fields.'.$field->id) <span class="text-sm text-blush">{{ $message }}</span> @enderror
                </label>
            @endforeach
            <label class="flex items-start gap-2 text-sm sm:col-span-2">
                <input type="checkbox" wire:model="consent" class="mt-1">
                <span>{{ __('J’accepte que l’ACA traite ce message conformément à la') }} <a href="{{ aca_url('privacy') }}" class="underline">{{ __('politique de vie privée') }}</a>.</span>
            </label>
            @error('consent') <p class="text-sm text-blush sm:col-span-2">{{ $message }}</p> @enderror
            @error('form') <p class="text-sm text-blush sm:col-span-2">{{ $message }}</p> @enderror
            <div class="sm:col-span-2">
                <button class="btn-plum" type="submit">{{ __('Envoyer') }}</button>
            </div>
        </form>
    @endif
</div>
