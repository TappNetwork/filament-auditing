<?php

use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Gate;

beforeEach(function () {
    $this->user = new class extends User {};
});

it('authorizes audit abilities when the audited model is available', function (string $ability) {
    expect(Gate::forUser($this->user)->allows($ability, $this->user))->toBeTrue();
})->with(['audit', 'restoreAudit']);

it('authorizes audit abilities when the audited model is unavailable', function (string $ability) {
    expect(Gate::forUser($this->user)->allows($ability))->toBeTrue();
})->with(['audit', 'restoreAudit']);

it('denies audit abilities for guests', function (string $ability) {
    expect(Gate::forUser(null)->allows($ability))->toBeFalse();
})->with(['audit', 'restoreAudit']);
