<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\Contracts\View\View;
use Closure;

/* class UserInfo extends Component
{
    public function __construct(
        public ?string $imgSrc = null,
        public string $fullName = '',
        public string $role = '',
    ) {}

    public function render(): View|Closure|string
    {
        return view('components.user-info');
    }
}
 */
class UserInfo extends Component
{
    public function __construct(
        public ?string $imgSrc = null,
        public string $fullName = '',
        public string $role = '',
        public int|string $userId = 0,   // ✅ جديد
    ) {}

    public function render(): View|Closure|string
    {
        return view('components.user-info');
    }
}

