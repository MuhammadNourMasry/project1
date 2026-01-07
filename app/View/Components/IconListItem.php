<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Illuminate\Support\Facades\Blade;
class IconListItem extends Component
{
    public function __construct(
        public string $srcIcon,
        public ?string $link = null,
        public ?string $srcIconHover = null
    ) {}

    public function render(): View|Closure|string
    {
        return view('components.icon-list-item');
    }
}
