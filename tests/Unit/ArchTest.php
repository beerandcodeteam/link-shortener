<?php

arch('models extend the Eloquent base model')
    ->expect('App\Models')
    ->toExtend('Illuminate\Database\Eloquent\Model')
    ->ignoring('App\Models\User');

arch('the User model extends the framework authenticatable base')
    ->expect('App\Models\User')
    ->toExtend('Illuminate\Foundation\Auth\User');

arch('no debugging helpers are left in the application code')
    ->expect(['dd', 'dump', 'ray', 'var_dump', 'dexit', 'ddd'])
    ->not->toBeUsed();

arch('livewire components live under the App\Livewire namespace and extend the base component')
    ->expect('App\Livewire')
    ->toExtend('Livewire\Component')
    ->ignoring('App\Livewire\Forms');

arch('livewire form objects extend the Livewire form base')
    ->expect('App\Livewire\Forms')
    ->toExtend('Livewire\Form');
