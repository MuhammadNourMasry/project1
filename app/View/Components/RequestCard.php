<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class RequestCard extends Component
{
    public function __construct(
        public $user
    ) {}

    public function render(): View|Closure|string
    {
        return view('components.request-card');
    }
}
