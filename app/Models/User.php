<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Traits\HasRoles;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    use BelongsToTenant;
    use HasRoles;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'tenant_id',
        "phone",
        "company_name",
        "biller_id",
        "warehouse_id",
        "is_active",
        "is_deleted"
    ];




    protected $hidden = [
        'password',
        'remember_token',
    ];


    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function isActive()
    {
        return $this->is_active;
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }


    public function holiday() {
        return $this->hasMany('App\Models\Holiday');
    }

    public function hasPermissionTo($permission, ?string $guardName = null):bool
    {
        // جلب جميع الأدوار النشطة للمستخدم
        $activeRoles = $this->roles()->where('is_active', true)->pluck('id');

        // إذا لم يكن لدى المستخدم أي دور نشط
        if ($activeRoles->isEmpty()) {
            return false;
        }

        // التحقق من وجود الصلاحية مع الأدوار النشطة فقط
        return DB::table('permissions')
            ->join('role_has_permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
            ->whereIn('role_has_permissions.role_id', $activeRoles)
            ->where('permissions.name', $permission)
            ->exists();
    }


    public function hasActiveRole($roleName): bool
    {
        return $this->roles()
            ->where('name', $roleName)
            ->where('is_active', true)
            ->exists();
    }

     /*if (!auth()->user()->hasActiveRole('admin')) {
     return redirect()->route('home')->with('error', 'Your role is inactive.');
      }*/
    public function getActivePermissions()
    {
        $activeRoles = $this->roles()->where('is_active', true)->pluck('id');

        return Permission::whereIn('id', function ($query) use ($activeRoles) {
            $query->select('permission_id')
                ->from('role_has_permissions')
                ->whereIn('role_id', $activeRoles);
        })->get();
    }
     /*يمكنك استخدام هذه الوظيفة لاسترجاع الصلاحيات المرتبطة بالأدوار النشطة فقط:
     $permissions = auth()->user()->getActivePermissions();
      foreach ($permissions as $permission) {
           echo $permission->name;
      }*/

    public function deactivateAllRoles(): void
    {
        $this->roles()->update(['is_active' => false]);
    }
     /*عند إيقاف حساب المستخدم، يمكنك استدعاء هذه الوظيفة لتعطيل جميع أدواره:
        $user = User::find($id);
        $user->deactivateAllRoles();
        */

    public function hasAnyPermission(array $permissions): bool
    {
        $activeRoles = $this->roles()->where('is_active', true)->pluck('id');

        return Permission::whereIn('name', $permissions)
            ->whereIn('id', function ($query) use ($activeRoles) {
                $query->select('permission_id')
                    ->from('role_has_permissions')
                    ->whereIn('role_id', $activeRoles);
            })
            ->exists();
    }
 /*التحقق إذا كان لدى المستخدم أي صلاحية من مجموعة معينة:
     if (auth()->user()->hasAnyPermission(['edit-post', 'delete-post'])) {
      // تنفيذ الإجراء
     }*/


    public function getActiveRoles()
    {
        return $this->roles()->where('is_active', true)->get();
    }
/*          لاسترجاع جميع الأدوار النشطة للمستخدم:
    $activeRoles = auth()->user()->getActiveRoles();

     foreach ($activeRoles as $role) {
       echo $role->name;
      }
      */

    public function hasAllPermissions(array $permissions): bool
    {
        $activeRoles = $this->roles()->where('is_active', true)->pluck('id');

        $permissionCount = Permission::whereIn('name', $permissions)
            ->whereIn('id', function ($query) use ($activeRoles) {
                $query->select('permission_id')
                    ->from('role_has_permissions')
                    ->whereIn('role_id', $activeRoles);
            })
            ->count();

        return $permissionCount === count($permissions);
    }


    public function toggleRoleStatus($roleName): void
    {
        $role = $this->roles()->where('id', $roleName)->first();

        if ($role) {
            $role->update(['is_active' => !$role->is_active]);
        }
    }



}
