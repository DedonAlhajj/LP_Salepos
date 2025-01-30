<?php
namespace App\Traits;
use Auth;

trait StaffAccess{

    public function staffAccessCheck($q)
    {
        if(Auth::user()->role_id > 2 && config('staff_access') == 'own')
            $q->where('user_id', Auth::id());
        elseif(Auth::user()->role_id > 2 && config('staff_access') == 'warehouse')
            $q->where('warehouse_id', Auth::user()->warehouse_id);
    }
}
