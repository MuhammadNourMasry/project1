<?php

namespace App\View\Components;

use App\Models\User;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class PendingDash extends Component
{
    public function __construct(
        public $users,
        // public string $link,
        public int $pendingUserCount
    ) {}

    public function render(): View|Closure|string
    {
        return view('components.pending-dash');
    }
}
