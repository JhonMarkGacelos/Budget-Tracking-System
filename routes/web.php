<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('welcome');
})->name('home');

Route::get('/welcome', function () {
    return view('welcome');
})->name('welcome');

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');

    Route::post('/login', function (Illuminate\Http\Request $request) {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            
            // Create API token for the authenticated user
            $user = Auth::user();
            $token = $user->createToken('web-token')->plainTextToken;
            
            return redirect()->route('dashboard')->with('api_token', $token);
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    });
});

// Protected Routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard.index');
    })->name('dashboard');

    Route::post('/logout', function (Illuminate\Http\Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('welcome');
    })->name('logout');

    // Budget Requests
    Route::get('/budget-requests', function () {
        return view('budget-requests.index');
    })->name('budget-requests.index');

    Route::get('/budget-requests/create', function () {
        return view('budget-requests.create');
    })->name('budget-requests.create');

    Route::get('/budget-requests/{id}', function ($id) {
        return view('budget-requests.show');
    })->name('budget-requests.show');

    // Users Management
    Route::get('/users', function () {
        return view('users.index');
    })->name('users.index');

    Route::get('/users/create', function () {
        return view('users.create');
    })->name('users.create');

    Route::get('/users/{id}', function ($id) {
        return view('users.show');
    })->name('users.show');

    Route::get('/users/{id}/edit', function ($id) {
        return view('users.edit');
    })->name('users.edit');

    // Budget Allocations
    Route::get('/budget-allocations', function () {
        return view('budget-allocations.index');
    })->name('budget-allocations.index');

    Route::get('/budget-allocations/create', function () {
        return view('budget-allocations.create');
    })->name('budget-allocations.create');

    Route::get('/budget-allocations/{id}', function ($id) {
        return view('budget-allocations.show');
    })->name('budget-allocations.show');

    // Audit Logs
    Route::get('/audit-logs', function () {
        return view('audit-logs.index');
    })->name('audit-logs.index');

    // Analytics Pages
    Route::get('/analytics/department', function () {
        return view('analytics.department');
    })->name('analytics.department');

    Route::get('/analytics/admin', function () {
        return view('analytics.admin');
    })->name('analytics.admin');

    // Debug Page
    Route::get('/debug', function () {
        return view('debug');
    })->name('debug');

    // Analytics Dashboard
    Route::controller(\App\Http\Controllers\AnalyticsController::class)->group(function () {
        Route::get('/analytics', 'dashboard')->name('analytics.dashboard');
        Route::get('/analytics/financial-report', function () {
            return view('analytics.financial-report');
        })->name('analytics.financial-report');
        Route::get('/analytics/spending-analysis', function () {
            return view('analytics.spending-analysis');
        })->name('analytics.spending-analysis');
        Route::get('/analytics/forecast-report', function () {
            return view('analytics.forecast-report');
        })->name('analytics.forecast-report');
    });
});
