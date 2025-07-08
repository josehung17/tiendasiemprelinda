<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

abstract class TestCase extends BaseTestCase
{
    // Este método se ejecuta antes de cada test
    protected function setUp(): void
    {
        parent::setUp();

        // Asegurarse de que los modelos de Spatie Permission se carguen
        // Esto puede ayudar a resolver errores de "Target class [role] does not exist"
        // al asegurar que los modelos estén en memoria.
        Role::all();
        Permission::all();
    }
}
