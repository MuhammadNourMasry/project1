@if ($link)
    <a href="{{ $link }}">
@else
    <button {{ $attributes }}>
@endif

    @isset($imgSrc)
        <img src="{{ $imgSrc }}" class="{{ $styleClass }}"/>
    @endisset

    {{ $slot }}

@if ($link)
    </a>
@else
    </button>
@endif
