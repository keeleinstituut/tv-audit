<?php

namespace Tests\Feature;

use AuditLogClient\Enums\AuditLogEventObjectType;
use Faker\Generator;

class ObjectIdentityCreators
{
    public static function buildObjectFromType(AuditLogEventObjectType $objectType): array
    {
        return match ($objectType) {
            AuditLogEventObjectType::InstitutionUser => ObjectIdentityCreators::institutionUser(),
            AuditLogEventObjectType::Role => ObjectIdentityCreators::role(),
            AuditLogEventObjectType::Institution => ObjectIdentityCreators::institution(),
            AuditLogEventObjectType::Vendor => ObjectIdentityCreators::vendor(),
            AuditLogEventObjectType::InstitutionDiscount => ObjectIdentityCreators::institutionDiscount(),
            AuditLogEventObjectType::Project => ObjectIdentityCreators::project(),
            AuditLogEventObjectType::Subproject => ObjectIdentityCreators::subproject(),
            AuditLogEventObjectType::Assignment => ObjectIdentityCreators::assignment(),
            AuditLogEventObjectType::TranslationMemory => ObjectIdentityCreators::translationMemory(),
        };
    }

    public static function institutionUser(): array
    {
        return [
            'id' => fake()->uuid(),
            'user' => [
                'id' => fake()->uuid(),
                'personal_identification_code' => '60007116568',
                'forename' => fake()->firstName(),
                'surname' => fake()->lastName(),
            ],
        ];
    }

    public static function role(): array
    {
        return [
            'id' => fake()->uuid(),
            'name' => fake()->colorName(),
        ];
    }

    public static function institution(): array
    {
        return [
            'id' => fake()->uuid(),
            'name' => fake()->company(),
        ];
    }

    public static function vendor(): array
    {
        return ['TODO' => null]; // TODO
    }

    public static function institutionDiscount(): array
    {
        return ['TODO' => null]; // TODO
    }

    public static function assignment(): array
    {
        return ['TODO' => null]; // TODO
    }

    public static function translationMemory(): array
    {
        return ['TODO' => null]; // TODO
    }

    public static function project(): array
    {
        return ['TODO' => null]; // TODO
    }

    public static function subproject(): array
    {
        return ['TODO' => null]; // TODO
    }
}
