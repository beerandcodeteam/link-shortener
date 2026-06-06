<?php

declare(strict_types=1);

use App\Models\Link;
use App\Services\ShortCodeGenerator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

it('generates codes matching the default charset and length', function (): void {
    $defaultLength = 7;
    $expectedCharset = str_split('abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789');

    for ($i = 0; $i < 10; $i++) {
        $code = ShortCodeGenerator::generate();

        expect(count(str_split($code)))->toBe($defaultLength);

        foreach (str_split($code) as $char) {
            expect(in_array($char, $expectedCharset))->toBeTrue();
        }
    }
});

it('generates codes with configurable length', function (): void {
    $length = 10;
    $code = ShortCodeGenerator::generate($length);
    $expectedLen = 10;

    expect(count(str_split($code)))->toBe($expectedLen);

    foreach (str_split($code) as $char) {
        expect(in_array($char, str_split('abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789')))->toBeTrue();
    }
});

it('generates codes matching a custom charset', function (): void {
    $customCharset = 'xyz123';
    $charsetArray = str_split($customCharset);
    $code = ShortCodeGenerator::generateFromCharset(8, $customCharset);

    expect(count(str_split($code)))->toBe(8);

    foreach (str_split($code) as $char) {
        expect(in_array($char, $charsetArray))->toBeTrue();
    }
});

it('generates unique codes across 100 iterations', function (): void {
    DB::table('users')->insert([
        'id' => 5001,
        'name' => 'Test User',
        'email' => 'test@test.com',
        'password' => bcrypt('password'),
        'email_verified_at' => now(),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    DB::table('link_statuses')->insert([
        'id' => 3, 'slug' => 'active', 'name' => 'Active',
        'description' => null, 'is_active' => true,
        'created_at' => now(), 'updated_at' => now(),
    ]);

    $codes = [];
    for ($i = 0; $i < 100; $i++) {
        $codes[] = ShortCodeGenerator::generateUnique();
    }

    expect($codes)->toHaveCount(count(array_unique($codes)));
});

it('validates generateFromCharset constraints', function (): void {
    expect(fn () => ShortCodeGenerator::generateFromCharset(0, 'xyz'))->toThrow(InvalidArgumentException::class);
    expect(fn () => ShortCodeGenerator::generateFromCharset(-1, 'xyz'))->toThrow(InvalidArgumentException::class);
    expect(fn () => ShortCodeGenerator::generateFromCharset(8, ''))->toThrow(InvalidArgumentException::class);
});

it('detects collision with seeded code and retries', function (): void {
    DB::table('users')->insert([
        'id' => 6001,
        'name' => 'Collision User',
        'email' => 'collision@test.com',
        'password' => bcrypt('password'),
        'email_verified_at' => now(),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    DB::table('link_statuses')->insert([
        'id' => 4,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    Link::create([
        'id' => 6001,
        'original_url' => 'https://example.com',
        'short_code' => 'collision_test',
        'user_id' => 6001,
        'link_status_id' => 4,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $codes = [];
    for ($i = 0; $i < 30; $i++) {
        $code = ShortCodeGenerator::generateUnique();

        expect($code)->not->toBe('collision_test');
        expect($codes)->not->toContain($code);

        $codes[] = $code;
    }
});
