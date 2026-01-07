<?php

namespace Tests\Unit;

use App\Models\CategoriaCliente;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoriaClienteTest extends TestCase
{
    use RefreshDatabase;

    public function test_model_usa_la_tabla_correcta(): void
    {
        $this->assertSame('categorias_clientes', (new CategoriaCliente)->getTable());
    }

    public function test_factory_crea_categoria_cliente(): void
    {
        $cat = CategoriaCliente::factory()->create();

        $this->assertNotNull($cat->id);
        $this->assertNotEmpty($cat->nombre);

        $this->assertDatabaseHas('categorias_clientes', [
            'id' => $cat->id,
            'nombre' => $cat->nombre,
        ]);
    }

    public function test_se_puede_crear_por_mass_assignment(): void
    {
        $cat = CategoriaCliente::create([
            'nombre' => 'VIP AAA',
            'descripcion' => 'Clientes de máxima prioridad',
        ]);

        $this->assertDatabaseHas('categorias_clientes', [
            'id' => $cat->id,
            'nombre' => 'VIP AAA',
        ]);
    }

    public function test_se_puede_actualizar_categoria(): void
    {
        $cat = CategoriaCliente::factory()->create([
            'nombre' => 'AAA',
        ]);

        $cat->update([
            'nombre' => 'AA',
            'descripcion' => 'Categoría actualizada',
        ]);

        $this->assertDatabaseHas('categorias_clientes', [
            'id' => $cat->id,
            'nombre' => 'AA',
            'descripcion' => 'Categoría actualizada',
        ]);
    }

    public function test_nombre_es_unico(): void
    {
        CategoriaCliente::create(['nombre' => 'VIP', 'descripcion' => null]);

        $this->expectException(QueryException::class);

        // debe fallar por unique
        CategoriaCliente::create(['nombre' => 'VIP', 'descripcion' => 'duplicado']);
    }

    public function test_relacion_clientes_es_hasMany(): void
    {
        $cat = CategoriaCliente::factory()->create();

        $this->assertInstanceOf(HasMany::class, $cat->clientes());
    }

    public function test_nombre_es_obligatorio_a_nivel_bd(): void
    {
        $this->expectException(QueryException::class);

        CategoriaCliente::query()->create([
            'nombre' => null,
            'descripcion' => 'x',
        ]);
    }

    public function test_relacion_clientes_devuelve_registros(): void
    {
        $cat = CategoriaCliente::factory()->create();

        \App\Models\Cliente::factory()->count(2)->create([
            'categoria_id' => $cat->id,
        ]);

        $cat->refresh();

        $this->assertCount(2, $cat->clientes);
    }

}
