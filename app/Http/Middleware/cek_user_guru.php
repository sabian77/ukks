<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User; // Ensure the model name is capitalized
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class cek_user_guru
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $UserEmail = Auth::user()->email;

            // Check if the user's email exists in the guru table
            $guruExists = User::where('email', $UserEmail)->exists();

            // If the user is authenticated but not found in the guru table
            if (!$guruExists) {
                Auth::logout(); // Log out the user
                return redirect('/login')->with('error', 'Email tidak cocok dengan data di tabel guru');
            }
        }

        return $next($request);
    }
}
