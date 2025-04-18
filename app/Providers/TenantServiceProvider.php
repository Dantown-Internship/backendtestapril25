<?php

// app/Providers/TenantServiceProvider.php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;
use Illuminate\Support\Facades\Auth;

class TenantServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Sanctum::getAccessTokenFromRequestUsing(function ($request) {
            $token = $request->bearerToken();
            if ($token) {
                $tokenModel = Sanctum::$personalAccessTokenModel::findToken($token);
                if ($tokenModel && in_array($request->tenant_id, $tokenModel->abilities)) {
                    return $token;
                }
            }
            return null;
        });
    }
}