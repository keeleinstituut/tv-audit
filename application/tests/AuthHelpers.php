<?php

namespace Tests;

use App\Enums\PrivilegeKey;
use Faker\Generator;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

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

    public static function generateAccessToken(array $tolkevaravPayload): string
    {
        $payload = collect([
            'azp' => Str::of(config('keycloak.accepted_authorized_parties'))
                ->explode(',')
                ->first(),
            'iss' => config('keycloak.base_url').'/realms/'.config('keycloak.realm'),
            'tolkevarav' => $tolkevaravPayload,
        ]);

        return static::createJwt($payload->toArray());
    }

    public static function createJwt(array $payload): string
    {
        $privateKeyPem = static::getPrivateKey();

        return JWT::encode($payload, $privateKeyPem, 'RS256');
    }

    public static function createAuthHeaders(array $tolkevaravClaims): array
    {
        $accessToken = static::generateAccessToken($tolkevaravClaims);

        return ['Authorization' => "Bearer $accessToken"];
    }

    public static function createTolkevaravClaims(string $institutionId, PrivilegeKey $privilege): array
    {
        return [
            'personalIdentificationCode' => app(Generator::class)->estonianPIC(),
            'userId' => fake()->uuid(),
            'institutionUserId' => fake()->uuid(),
            'forename' => fake()->firstName(),
            'surname' => fake()->lastName(),
            'selectedInstitution' => [
                'id' => $institutionId,
                'name' => fake()->company(),
            ],
            'privileges' => [$privilege->value],
        ];
    }
}
