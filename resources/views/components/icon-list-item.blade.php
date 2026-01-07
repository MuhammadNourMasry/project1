@php
    $class = $attributes->get('class', '');   // بيرجع سترنغ الكلاسات
    $isActive = str_contains(" $class ", ' active ');
@endphp

<li {{ $attributes }}>
    <img
        src="{{ $isActive ? $srcIconHover : $srcIcon }}"
        width="20" height="20";
    />

    @if ($link)
        <a href="{{ $link }}">
            {{ $slot }}
        </a>
    @else
        <p>{{ $slot }}</p>
    @endif
</li>
