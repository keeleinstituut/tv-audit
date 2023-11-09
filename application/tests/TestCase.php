<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Arr;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use RefreshDatabase;

    public static function assertArrayHasSubsetIgnoringOrder(?array $expectedSubset, ?array $actual): void
    {
        static::assertNotNull($expectedSubset);
        static::assertNotNull($actual);

        $sortedDottedExpectedSubset = Arr::dot(Arr::sortRecursive($expectedSubset));
        $sortedDottedActualWholeArray = Arr::dot(Arr::sortRecursive($actual));
        $sortedDottedActualSubset = Arr::only($sortedDottedActualWholeArray, array_keys($sortedDottedExpectedSubset));

        static::assertArraysEqualIgnoringOrder($sortedDottedExpectedSubset, $sortedDottedActualSubset);
    }

    public static function assertArraysEqualIgnoringOrder(?array $expected, ?array $actual): void
    {
        static::assertNotNull($expected);
        static::assertNotNull($actual);

        static::assertEquals(
            Arr::sortRecursive($expected),
            Arr::sortRecursive($actual)
        );
    }

    public static function areArraysEqualIgnoringOrder(?array $expected, ?array $actual): bool
    {
        return $expected !== null
            && $actual !== null
            && Arr::sortRecursive($expected) === Arr::sortRecursive($actual);
    }
}
