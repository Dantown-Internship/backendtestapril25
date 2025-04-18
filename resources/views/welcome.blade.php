<?php

use Illuminate\Support\Facades\Route;

// ... other code ...

// Assuming the error was caused by using Route:: without importing it
// or without the full namespace in the Blade template.

// The fix involves ensuring the Route facade is accessible.
// In Blade templates, the most robust way is often to use the full namespace
// or ensure the facade alias is correctly configured in config/app.php.
// However, since the error is "Class 'Route' not found", it implies
// the alias isn't working or the class isn't being found for some reason.
// Using the full namespace directly in the Blade file is a common fix.

// Example of the problematic line in welcome.blade.php (around line 24):
// @if (Route::has('login'))

// Corrected version:
// @if (\Illuminate\Support\Facades\Route::has('login'))

// Or, if the issue is within a standard Laravel welcome view structure:

?>
{{-- resources/views/welcome.blade.php --}}
{{-- Assuming the selection includes the part causing the error --}}

{{-- Start of Selection --}}
@if (\Illuminate\Support\Facades\Route::has('login'))
    <nav class="-mx-3 flex flex-1 justify-end">
        @auth
            <a
                href="{{ url('/dashboard') }}"
                class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
            >
                Dashboard
            </a>
        @else
            <a
                href="{{ route('login') }}"
                class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
            >
                Log in
            </a>

            @if (\Illuminate\Support\Facades\Route::has('register'))
                <a
                    href="{{ route('register') }}"
                    class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                >
                    Register
                </a>
            @endif
        @endauth
    </nav>
@endif
{{-- End of Selection --}}
