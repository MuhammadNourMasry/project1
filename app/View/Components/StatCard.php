<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\HtmlString;
use Illuminate\View\Component;

class StatCard extends Component
{
    public string $rangeText, $selectedRange;
    public function __construct(
        public string $statTitle,
        public string $statValue,
        public string $statIcon,
        public float $statRatio,
        public string $period = 'rolling30',
        public string $key = ''
    ) {
        $this->rangeText = ($statRatio >= 0 ? '↑ ' : '↓ ');
        $this->rangeText .= $period == 'mtd'
            ? "$statRatio% from last month"
            : "$statRatio% from last 30 days";

        $this->selectedRange = $period == 'mtd'
            ? ' MTD prior'
            : ' Rolling 30';
    }
    public function render(): View|Closure|string
    {
        return view('components.stat-card');
    }
}

// public string $labelRatio = '';
    // public bool $hasRatio = false;
    // public string $key;
    // public string $period;
    // public string $selectedText;



            // $colorClass = $isPositive ? 'text-green-600' : 'text-red-600';

            // $periodLabel = $period === 'mtd'
            //     ? 'from last month'
            //     : 'from last 30 days';


            // $this->labelRatio = new HtmlString(
            //     "<p class=\"{$colorClass}\">{$arrow} {$ratio}% {$periodLabel}</p>"
            // );
