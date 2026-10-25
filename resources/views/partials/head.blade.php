<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<title>
    {{ filled($title ?? null) ? $title.' - '.config('app.name', 'MediLoop') : config('app.name', 'MediLoop') }}
</title>

@include('partials.favicons')

@fonts

@vite(['resources/css/app.css', 'resources/js/app.js'])
@fluxAppearance
