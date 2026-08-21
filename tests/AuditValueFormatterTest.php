<?php

use Tapp\FilamentAuditing\Support\AuditValueFormatter;

it('formats nested related model maps by id', function () {
    expect(AuditValueFormatter::nested(['id' => 42]))->toBe('42');
});

it('formats scalar relationship ids', function (mixed $value, string $expected) {
    expect(AuditValueFormatter::nested($value))->toBe($expected);
})->with([
    'integer' => [7, '7'],
    'string' => ['01abc', '01abc'],
    'true' => [true, 'true'],
    'false' => [false, 'false'],
    'null' => [null, ''],
]);

it('json-encodes nested arrays without an id key', function () {
    expect(AuditValueFormatter::nested(['name' => 'Coach']))->toBe('{"name":"Coach"}');
});
