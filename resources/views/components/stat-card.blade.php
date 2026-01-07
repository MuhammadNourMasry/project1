<div class="stat-card">
    <div class="stat-header">
        <div class="stat-info">
            <p class="stat-title">{{ $statTitle }}</p>
            <p class="stat-value">{{ $statValue }}</p>
        </div>
        <img src="{{ $statIcon }}" width="40" height="40" alt="Users icon">
    </div>


    <div class="stat-footer">
        <p style = "color: {{ $statRatio >= 0 ? 'rgb(4, 176, 4)' : 'red' }};">
            {{ $rangeText }}
        </p>

        <details class="drowpdown">
            <summary class="selected">
                <span>{{ $selectedRange }}</span>
                <span class="chev">▾</span>
            </summary>

            <div class="options">
                <a class="range {{ $period === 'rolling30' ? 'is-active' : '' }}"
                    href="{{ route('index', array_merge(request()->query(), [ $key.'_period' => 'rolling30' ])) }}"
                >
                    Rolling 30
                </a>

                <a class="range {{ $period === 'mtd' ? 'is-active' : '' }}"
                    href="{{ route('index', array_merge(request()->query(), [ $key.'_period' => 'mtd' ])) }}"
                >
                    MTD prior
                </a>
            </div>
        </details>
    </div>


</div>
