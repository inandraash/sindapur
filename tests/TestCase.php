<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Seed roles untuk test
        \App\Models\Role::firstOrCreate(['id' => 1], ['nama_role' => 'Admin']);
        \App\Models\Role::firstOrCreate(['id' => 2], ['nama_role' => 'Staf Dapur']);
    }
}
