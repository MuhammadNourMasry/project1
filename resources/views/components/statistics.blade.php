<div class="statistics">
    @foreach ($stats as $stat)
        <x-stat-card
            :stat-title="$stat['title']"
            :stat-value="$stat['value']"
            :stat-icon="Storage::url($stat['icon'])"
            :stat-ratio="$stat['ratio']"
            :period="$stat['period']"
            :key="$stat['key']"
        />
    @endforeach
</div>
