<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureDoctorRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return redirect()->route('filament.doctor.auth.login');
        }

        $user = Auth::user();

        if (! $user->isDoctor() || ! $user->doctor) {
            Auth::logout();
            return redirect()->route('filament.doctor.auth.login')
                ->withErrors(['email' => __('doctor.access_denied')]);
        }

        return $next($request);
    }
}
