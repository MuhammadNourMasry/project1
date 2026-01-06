<ul class="user-details">
    @foreach($properties as $index => $prop)
        <x-property-item
            class="property {{ $index === 2 ? 'id-card' : '' }}"
            :src-icon="Storage::url($prop['src_icon'])"
        >
            {{ $prop['text'] }}
        </x-property-item>
    @endforeach
</ul>
