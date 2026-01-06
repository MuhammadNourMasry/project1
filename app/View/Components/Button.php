<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Button extends Component
{
    public function __construct(
        public ?string $imgSrc = null,
        public ?string $link = null,
        public ?string $styleClass = null
    ) {}
    public function render()
    {
        return view('components.button');
    }
}
