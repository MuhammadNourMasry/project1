<?php

namespace App\Http\Controllers;

use App\Models\Apartment;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware(function ($request, $next) {
    //         if (Auth::check() && !Auth::user()->isAdmin()) {
    //             Auth::logout();
    //             return redirect()->route('login.form')
    //                 ->withErrors(['email' => 'You are not allowed to access this panel.']);
    //         }
    //         return $next($request);
    //     })->except(['showLogin', 'login']);
    // }
    public function loginPage()
    {
        return view('dashboard.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            if (!Auth::user()->isAdmin()) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'email' => 'You are not allowed to access this panel.'
                ])->onlyInput('email');
            }

            return redirect()->route('index');
        }

        return back()
            ->withErrors([
                'email' => 'Email or password is incorrect.'
            ])
            ->onlyInput('email');
    }

    public function logout(Request $request) {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function approve(User $user)
    {
        $user->update(['is_approved' => true,]);
        return back();
    }

    public function deleteUser(User $user)
    {
        $user->delete();
        return back();
    }

    public function getAllUser()
    {
        $users = User::nonAdmin()->approved()->get();
        return view('dashboard.user-management', compact('users'));
    }

    public function getAllPendingUser()
    {
        $users = User::pending()->get();
        return view('dashboard.registration-requests', compact('users'));
    }

    public function index(Request $request)
    {
        $periodUsers      = $this->normalizePeriod($request->query('users_period', 'rolling30'));
        $periodApartments = $this->normalizePeriod($request->query('apartments_period', 'rolling30'));
        $periodBookings   = $this->normalizePeriod($request->query('bookings_period', 'rolling30'));

        $stats = [
            [
                'key'    => 'users',
                'title'  => 'Total Users',
                'icon'   => 'icons/group.svg',
                'value'  => User::approved()->nonAdmin()->count(),
                'period' => $periodUsers,
                'ratio'  => $this->ratioFor(User::class, $periodUsers),
            ],
            [
                'key'    => 'apartments',
                'title'  => 'Total Apartment',
                'icon'   => 'icons/home.svg',
                'value'  => Apartment::count(),
                'period' => $periodApartments,
                'ratio'  => $this->ratioFor(Apartment::class, $periodApartments),
            ],
            [
                'key'    => 'bookings',
                'title'  => 'Total Booking',
                'icon'   => 'icons/booking.svg',
                'value'  => Booking::completed()->count(),
                'period' => $periodBookings,
                'ratio'  => $this->ratioFor(Booking::class, $periodBookings),
            ]
        ];

        $users = User::pending()->inRandomOrder()->limit(3)->get();
        $pendingUserCount = User::pending()->count();

        return view('dashboard.index', compact('stats', 'users', 'pendingUserCount'));
    }

    private function normalizePeriod(string $period): string
    {
        return in_array($period, ['rolling30', 'mtd'], true) ? $period : 'rolling30';
    }


    private function ratioFor(string $modelClass, string $period): float
    {
        if ($period === 'mtd') {
            return $this->mtdPrior($modelClass);
        }
        return $this->rolling30($modelClass);
    }

    private function rolling30(string $modelClass): float
    {
        $now = now()->toImmutable();
        $currentStart  = $now->subDays(30);
        $previousStart = $now->subDays(60);

        $query = $modelClass::query();

        if ($modelClass === User::class) {
            $query->approved()->nonAdmin();
        }

        if ($modelClass === Booking::class) {
            $query->completed();
        }

        $current  = (clone $query)->createdBetween($currentStart, $now)->count();
        $previous = (clone $query)->createdBetween($previousStart, $currentStart)->count();

        return $previous === 0
            ? 0.0
            : round((($current - $previous) / $previous) * 100, 2);
    }


    private function mtdPrior(string $modelClass): float
    {
        $now = now()->toImmutable();

        $startThisMonth = $now->startOfMonth();
        $sameDayLastMonth = $now->subMonthNoOverflow();
        $startLastMonth = $sameDayLastMonth->startOfMonth();

        $query = $modelClass::query();

        if ($modelClass === User::class) {
            $query->approved()->nonAdmin();
        }

        if ($modelClass === Booking::class) {
            $query->completed();
        }

        $current  = (clone $query)->createdBetween($startThisMonth, $now)->count();
        $previous = (clone $query)->createdBetween($startLastMonth, $sameDayLastMonth)->count();

        return $previous === 0
            ? 0.0
            : round((($current - $previous) / $previous) * 100, 2);
    }

}



    // public function getCountUser() {
    //     return User::approved()->count()-1;
    // }
    // public function getCountPendingUser() {
    //     return User::pending()->count();
    // }
    // public function getCountApartment() {
    //     return Apartment::count();
    // }
    // public function getCountBookings() {
    //     return Booking::completed()->count();
    // }

    // public function get3RandomUsers() {
    //     $users = User::inRandomOrder()->limit(3)->get();
    //     return view('components.pending-dash', compact('users'));
    // }

    // public function getStatistics(Request $request)
    // {
    //     $periodUsers      = $this->normalizePeriod($request->query('users_period', 'rolling30'));
    //     $periodApartments = $this->normalizePeriod($request->query('apartments_period', 'rolling30'));
    //     $periodBookings   = $this->normalizePeriod($request->query('bookings_period', 'rolling30'));

    //     $stats = [
    //         [
    //             'key'    => 'users',
    //             'title'  => 'Total Users',
    //             'icon'   => 'icons/group.svg',
    //             'value'  => User::approved()->count() - 1,
    //             'period' => $periodUsers,
    //             'ratio'  => $this->ratioFor(User::class, $periodUsers),
    //         ],
    //         [
    //             'key'    => 'apartments',
    //             'title'  => 'Total Apartment',
    //             'icon'   => 'icons/home.svg',
    //             'value'  => Apartment::count(),
    //             'period' => $periodApartments,
    //             'ratio'  => $this->ratioFor(Apartment::class, $periodApartments),
    //         ],
    //         [
    //             'key'    => 'bookings',
    //             'title'  => 'Total Booking',
    //             'icon'   => 'icons/booking.svg',
    //             'value'  => Booking::completed()->count(),
    //             'period' => $periodBookings,
    //             'ratio'  => $this->ratioFor(Booking::class, $periodBookings),
    //         ],
    //         [
    //             'value'  => User::pending()->count()
    //         ]
    //     ];

    //     return view('components.statistics', compact('stats'));
    // }
