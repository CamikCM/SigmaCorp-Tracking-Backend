<?php

namespace App\Policies;

use App\Models\Usuario;
use Spatie\Permission\Models\Role;
use Illuminate\Auth\Access\HandlesAuthorization;

class RolePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the usuario can view any models.
     */
    public function viewAny(Usuario $usuario): bool
    {
        return $usuario->can('view_any_role');
    }

    /**
     * Determine whether the usuario can view the model.
     */
    public function view(Usuario $usuario, Role $role): bool
    {
        return $usuario->can('view_role');
    }

    /**
     * Determine whether the usuario can create models.
     */
    public function create(Usuario $usuario): bool
    {
        return $usuario->can('create_role');
    }

    /**
     * Determine whether the usuario can update the model.
     */
    public function update(Usuario $usuario, Role $role): bool
    {
        return $usuario->can('update_role');
    }

    /**
     * Determine whether the usuario can delete the model.
     */
    public function delete(Usuario $usuario, Role $role): bool
    {
        return $usuario->can('delete_role');
    }

    /**
     * Determine whether the usuario can bulk delete.
     */
    public function deleteAny(Usuario $usuario): bool
    {
        return $usuario->can('delete_any_role');
    }

    /**
     * Determine whether the usuario can permanently delete.
     */
    public function forceDelete(Usuario $usuario, Role $role): bool
    {
        return $usuario->can('{{ ForceDelete }}');
    }

    /**
     * Determine whether the usuario can permanently bulk delete.
     */
    public function forceDeleteAny(Usuario $usuario): bool
    {
        return $usuario->can('{{ ForceDeleteAny }}');
    }

    /**
     * Determine whether the usuario can restore.
     */
    public function restore(Usuario $usuario, Role $role): bool
    {
        return $usuario->can('{{ Restore }}');
    }

    /**
     * Determine whether the usuario can bulk restore.
     */
    public function restoreAny(Usuario $usuario): bool
    {
        return $usuario->can('{{ RestoreAny }}');
    }

    /**
     * Determine whether the usuario can replicate.
     */
    public function replicate(Usuario $usuario, Role $role): bool
    {
        return $usuario->can('{{ Replicate }}');
    }

    /**
     * Determine whether the usuario can reorder.
     */
    public function reorder(Usuario $usuario): bool
    {
        return $usuario->can('{{ Reorder }}');
    }
}
