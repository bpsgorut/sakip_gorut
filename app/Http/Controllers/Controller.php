<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Get the appropriate view folder based on user role
     *
     * @return string
     */
    protected function getViewFolder()
    {
        if (!Auth::check()) {
            return 'super admin'; // Default fallback
        }

        $user = Auth::user();
        $roleId = $user->role_id;

        switch ($roleId) {
            case 1: // Super Admin
                return 'super admin';
            case 2: // Admin
                return 'admin';
            case 3: // Ketua Tim
                return 'ketua tim';
            case 4: // User/Anggota Tim
                return 'anggota tim';
            default:
                return 'super admin'; // Default fallback
        }
    }

    /**
     * Helper method to get role-based view path
     *
     * @param string $viewPath
     * @return string
     */
    protected function roleView($viewPath)
    {
        $folder = $this->getViewFolder();
        return $folder . '.' . $viewPath;
    }
}
