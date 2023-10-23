<?php

namespace Tests;

use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Http;

readonly class AuthHelpers
{
    public static function fakeServiceValidationResponse(): void
    {
        Http::fake([
            rtrim(config('keycloak.base_url'), '/').'/*' => Http::response([
                'access_token' => AuthHelpers::generateServiceAccountJwt(),
                'expires_in' => 300,
            ]),
        ]);
    }

    public static function generateServiceAccountJwt(string $role = null, int $expiresIn = null): string
    {
        return JWT::encode([
            'iss' => config('keycloak.base_url').'/realms/'.config('keycloak.realm'),
            'exp' => time() + ($expiresIn ?: 300),
            'realm_access' => [
                'roles' => [$role ?: config('keycloak.service_account_sync_role')],
            ],
        ], static::getPrivateKey(), 'RS256');
    }

    private static function getPrivateKey(): string
    {
        $key = env('KEYCLOAK_REALM_PRIVATE_KEY');

        return "-----BEGIN PRIVATE KEY-----\n".
            wordwrap($key, 64, "\n", true).
            "\n-----END PRIVATE KEY-----";
    }
}
