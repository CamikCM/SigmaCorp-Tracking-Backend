<?php

use App\Models\Usuario;

return [
    'shield_resource' => [
        'should_register_navigation' => true,
        'slug' => 'shield/roles',

        'navigation_sort' => -1,
        'navigation_badge' => true,

        // Ponlo como texto (mejor que boolean)
        'navigation_group' => 'Seguridad',

        'sub_navigation_position' => null,
        'is_globally_searchable' => false,
        'show_model_path' => true,

        // ✅ No tienes tenancy, déjalo en false
        'is_scoped_to_tenant' => false,

        'cluster' => null,
    ],

    'tenant_model' => null,

    // ✅ Tu modelo real del proyecto tracking
    'auth_provider_model' => [
        'fqcn' => Usuario::class,
    ],

    'super_admin' => [
        'enabled' => true,
        'name' => 'super_admin',
        'define_via_gate' => false,
        'intercept_gate' => 'before',
    ],

    'panel_user' => [
        'enabled' => true,
        'name' => 'panel_user',
    ],

    'permission_prefixes' => [
        'resource' => [
            'view',
            'view_any',
            'create',
            'update',
            'restore',
            'restore_any',
            'replicate',
            'reorder',
            'delete',
            'delete_any',
            'force_delete',
            'force_delete_any',
        ],
        'page' => 'page',
        'widget' => 'widget',
    ],

    'entities' => [
        'pages' => true,
        'widgets' => true,
        'resources' => true,

        // ✅ Activa permisos custom para acceso a paneles
        'custom_permissions' => true,
    ],

    // ✅ Aquí van tus permisos custom (los que tú usabas en otros proyectos)
    'custom_permissions' => [
        'access_informatica_panel' => 'Acceso al panel Informática',
        'access_supervisor_panel' => 'Acceso al panel Supervisor',
    ],

    'generator' => [
        'option' => 'policies_and_permissions',
        'policy_directory' => 'Policies',
        'policy_namespace' => 'Policies',
    ],

    'exclude' => [
        'enabled' => true,
        'pages' => [
            'Dashboard',
        ],
        'widgets' => [
            'AccountWidget',
            'FilamentInfoWidget',
        ],
        'resources' => [],
    ],

    // ✅ Para multi-panel, ponlo en true (si no, shield solo “ve” parte del panel default)
    'discovery' => [
        'discover_all_resources' => true,
        'discover_all_widgets' => true,
        'discover_all_pages' => true,
    ],

    'register_role_policy' => [
        'enabled' => true,
    ],
];
