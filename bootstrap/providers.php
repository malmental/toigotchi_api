<?php

use App\Providers\AppServiceProvider;
use Illuminate\Hashing\HashServiceProvider;
use Laravel\Passport\AuthServiceProvider;

return [
    AuthServiceProvider::class,
    AppServiceProvider::class,
    HashServiceProvider::class,
];
