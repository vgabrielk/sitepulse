<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'permissions',
        'is_active',
    ];

    protected $casts = [
        'permissions' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get all users with this role
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'role_user');
    }

    /**
     * Check if role has a specific permission
     */
    public function hasPermission(string $permission): bool
    {
        if (!$this->permissions) {
            return false;
        }

        return in_array($permission, $this->permissions) || in_array('*', $this->permissions);
    }

    /**
     * Add permission to role
     */
    public function addPermission(string $permission): void
    {
        $permissions = $this->permissions ?? [];
        
        if (!in_array($permission, $permissions)) {
            $permissions[] = $permission;
            $this->update(['permissions' => $permissions]);
        }
    }

    /**
     * Remove permission from role
     */
    public function removePermission(string $permission): void
    {
        $permissions = $this->permissions ?? [];
        
        $permissions = array_filter($permissions, fn($p) => $p !== $permission);
        $this->update(['permissions' => array_values($permissions)]);
    }

    /**
     * Get all available permissions
     */
    public static function getAvailablePermissions(): array
    {
        return [
            // Admin permissions
            'admin.access' => 'Acesso ao painel administrativo',
            'admin.dashboard' => 'Visualizar dashboard administrativo',
            
            // Client management
            'clients.view' => 'Visualizar clientes',
            'clients.create' => 'Criar clientes',
            'clients.edit' => 'Editar clientes',
            'clients.delete' => 'Excluir clientes',
            'clients.manage' => 'Gerenciar clientes (todas as operações)',
            
            // Site management
            'sites.view_all' => 'Visualizar todos os sites',
            'sites.manage_all' => 'Gerenciar todos os sites',
            'sites.delete_any' => 'Excluir qualquer site',
            
            // Review management
            'reviews.moderate' => 'Moderar reviews',
            'reviews.approve' => 'Aprovar reviews',
            'reviews.reject' => 'Rejeitar reviews',
            'reviews.delete' => 'Excluir reviews',
            
            // System management
            'system.settings' => 'Configurar sistema',
            'system.users' => 'Gerenciar usuários do sistema',
            'system.roles' => 'Gerenciar roles e permissões',
            'system.logs' => 'Visualizar logs do sistema',
            
            // Analytics
            'analytics.view_all' => 'Visualizar analytics de todos os clientes',
            'analytics.export' => 'Exportar dados de analytics',
            
            // Billing
            'billing.manage' => 'Gerenciar cobrança',
            'billing.plans' => 'Gerenciar planos',
            'billing.invoices' => 'Gerenciar faturas',
        ];
    }

    /**
     * Create default roles
     */
    public static function createDefaultRoles(): void
    {
        // Super Admin role
        self::firstOrCreate(
            ['slug' => 'super-admin'],
            [
                'name' => 'Super Administrador',
                'description' => 'Acesso total ao sistema',
                'permissions' => ['*'], // All permissions
                'is_active' => true,
            ]
        );

        // Admin role
        self::firstOrCreate(
            ['slug' => 'admin'],
            [
                'name' => 'Administrador',
                'description' => 'Acesso administrativo completo',
                'permissions' => [
                    'admin.access',
                    'admin.dashboard',
                    'clients.manage',
                    'sites.manage_all',
                    'reviews.moderate',
                    'analytics.view_all',
                ],
                'is_active' => true,
            ]
        );

        // Moderator role
        self::firstOrCreate(
            ['slug' => 'moderator'],
            [
                'name' => 'Moderador',
                'description' => 'Pode moderar conteúdo e visualizar dados',
                'permissions' => [
                    'admin.access',
                    'reviews.moderate',
                    'sites.view_all',
                    'analytics.view_all',
                ],
                'is_active' => true,
            ]
        );

        // Support role
        self::firstOrCreate(
            ['slug' => 'support'],
            [
                'name' => 'Suporte',
                'description' => 'Acesso limitado para suporte',
                'permissions' => [
                    'clients.view',
                    'sites.view_all',
                    'analytics.view_all',
                ],
                'is_active' => true,
            ]
        );
    }
}