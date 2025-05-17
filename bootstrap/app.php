<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// ROUTING , MIDDLEWARE, EXCEPTIONS

// ==================== ROUTING ====================
// Routing adalah pengaturan jalur URL yang akan mengarah ke controller atau fungsi tertentu.
// Routing ini akan menentukan bagaimana aplikasi akan merespons permintaan HTTP dari pengguna.
// Routing ini juga dapat mengatur middleware yang akan diterapkan pada setiap jalur.
// Routing ini juga dapat mengatur pengecualian yang akan ditangani oleh aplikasi.
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )

    // ==================== MIDDLEWARE ====================
    // Middleware yang digunakan untuk semua route
    // Middleware ini akan dieksekusi sebelum route yang ditentukan
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            // Middleware alias satuan, bisa diakses dengan $this->middleware('alias')
            'api' => \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
            'bindings' => \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);
        $middleware->append([
            // Tambahkan global middleware jika perlu
        ]);
    })
    // ==================== EXCEPTIONS ====================
    ->withExceptions(function (Exceptions $exceptions) {
        // Penanganan exception dapat ditambahkan di sini
    })
    ->create();
