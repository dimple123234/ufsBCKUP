<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        // If you want to exclude certain routes from CSRF protection
        // '/profile/upload-picture'
        '/register',
        '/login',
        '/logout',
        '/products',
        '/products/*'
    ];
} 