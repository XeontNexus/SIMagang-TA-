<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Main dashboard redirect based on user role
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        return $this->redirectBasedOnRole($user);
    }

    /**
     * Student dashboard
     */
    public function siswa(Request $request)
    {
        $user = Auth::user();
        
        return view('dashboard.siswa', [
            'user' => $user,
            'title' => 'Dashboard Siswa'
        ]);
    }

    /**
     * Teacher dashboard
     */
    public function guru(Request $request)
    {
        $user = Auth::user();
        
        return view('dashboard.guru', [
            'user' => $user,
            'title' => 'Dashboard Guru'
        ]);
    }

    /**
     * Company dashboard
     */
    public function dudi(Request $request)
    {
        $user = Auth::user();
        
        return view('dashboard.dudi', [
            'user' => $user,
            'title' => 'Dashboard DUDI'
        ]);
    }

    /**
     * Admin dashboard
     */
    public function admin(Request $request)
    {
        $user = Auth::user();
        
        return view('dashboard.admin', [
            'user' => $user,
            'title' => 'Dashboard Admin'
        ]);
    }

    /**
     * Redirect user based on their role
     */
    private function redirectBasedOnRole($user)
    {
        $role = $user->role ?? 'siswa';
        
        switch ($role) {
            case 'siswa':
                return redirect()->route('dashboard.siswa');
            case 'guru':
                return redirect()->route('dashboard.guru');
            case 'dudi':
                return redirect()->route('dashboard.dudi');
            case 'admin':
                return redirect()->route('dashboard.admin');
            default:
                return redirect()->route('dashboard.siswa');
        }
    }
}
