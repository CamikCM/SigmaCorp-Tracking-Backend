<?php

namespace App\Policies;

use App\Models\Usuario;
use App\Models\CategoriaCliente;
use Illuminate\Auth\Access\HandlesAuthorization;

class CategoriaClientePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the usuario can view any models.
     */
    public function viewAny(Usuario $usuario): bool
    {
        return $usuario->can('view_any_categoria::cliente');
    }

    /**
     * Determine whether the usuario can view the model.
     */
    public function view(Usuario $usuario, CategoriaCliente $categoriaCliente): bool
    {
        return $usuario->can('view_categoria::cliente');
    }

    /**
     * Determine whether the usuario can create models.
     */
    public function create(Usuario $usuario): bool
    {
        return $usuario->can('create_categoria::cliente');
    }

    /**
     * Determine whether the usuario can update the model.
     */
    public function update(Usuario $usuario, CategoriaCliente $categoriaCliente): bool
    {
        return $usuario->can('update_categoria::cliente');
    }

    /**
     * Determine whether the usuario can delete the model.
     */
    public function delete(Usuario $usuario, CategoriaCliente $categoriaCliente): bool
    {
        return $usuario->can('delete_categoria::cliente');
    }

    /**
     * Determine whether the usuario can bulk delete.
     */
    public function deleteAny(Usuario $usuario): bool
    {
        return $usuario->can('delete_any_categoria::cliente');
    }

    /**
     * Determine whether the usuario can permanently delete.
     */
    public function forceDelete(Usuario $usuario, CategoriaCliente $categoriaCliente): bool
    {
        return $usuario->can('force_delete_categoria::cliente');
    }

    /**
     * Determine whether the usuario can permanently bulk delete.
     */
    public function forceDeleteAny(Usuario $usuario): bool
    {
        return $usuario->can('force_delete_any_categoria::cliente');
    }

    /**
     * Determine whether the usuario can restore.
     */
    public function restore(Usuario $usuario, CategoriaCliente $categoriaCliente): bool
    {
        return $usuario->can('restore_categoria::cliente');
    }

    /**
     * Determine whether the usuario can bulk restore.
     */
    public function restoreAny(Usuario $usuario): bool
    {
        return $usuario->can('restore_any_categoria::cliente');
    }

    /**
     * Determine whether the usuario can replicate.
     */
    public function replicate(Usuario $usuario, CategoriaCliente $categoriaCliente): bool
    {
        return $usuario->can('replicate_categoria::cliente');
    }

    /**
     * Determine whether the usuario can reorder.
     */
    public function reorder(Usuario $usuario): bool
    {
        return $usuario->can('reorder_categoria::cliente');
    }
}
