<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PwaController extends Controller
{
    /**
     * Dedicated PWA Entry Route: /app
     * Ensures app users land directly in their respective dashboard,
     * or on a focused Student/Teacher login screen with no external dead links.
     */
    public function index(Request $request)
    {
        if (Auth::check()) {
            $user = Auth::user();
            return redirect($user->dashboardRoute());
        }

        return view('pwa.entry');
    }
}
