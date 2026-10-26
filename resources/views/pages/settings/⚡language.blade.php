<?php

use App\Models\User;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Language settings')] class extends Component {
    public string $locale = 'en';

    public function mount(): void
    {
        $supportedLocales = array_keys((array) config('localization.supported'));
        $user = Auth::user();
        $locale = $user instanceof User ? $user->locale : null;

        $this->locale = in_array($locale, $supportedLocales, true)
            ? (string) $locale
            : (string) config('app.locale');
    }

    public function updateLocale(): void
    {
        $supportedLocales = array_keys((array) config('localization.supported'));

        $validated = $this->validate([
            'locale' => ['required', 'string', Rule::in($supportedLocales)],
        ]);

        $user = Auth::user();

        if ($user instanceof User) {
            $user->forceFill(['locale' => $validated['locale']])->save();
        }

        session(['locale' => $validated['locale']]);
        app()->setLocale($validated['locale']);

        Flux::toast(variant: 'success', text: __('settings.language.saved'));

        $this->redirect(route('language.edit'), navigate: false);
    }
};
?>

<section class="w-full">
    @include('partials.settings-heading')

    <flux:heading level="2" class="sr-only">{{ __('settings.language.title') }}</flux:heading>

    <x-pages::settings.layout :heading="__('settings.language.title')" :subheading="__('settings.language.subtitle')">
        <form wire:submit="updateLocale" class="my-6 w-full space-y-6">
            <flux:radio.group
                wire:model="locale"
                variant="cards"
                :label="__('settings.language.heading')"
                :description="__('settings.language.description')"
                class="max-md:flex-col"
            >
                @foreach (config('localization.supported') as $code => $label)
                    <flux:radio
                        value="{{ $code }}"
                        :description="$code === 'en'
                            ? __('settings.language.english_description')
                            : __('settings.language.indonesian_description')"
                    >
                        {{ $label }}
                    </flux:radio>
                @endforeach
            </flux:radio.group>

            <flux:button variant="primary" type="submit">
                {{ __('common.save_changes') }}
            </flux:button>
        </form>
    </x-pages::settings.layout>
</section>
