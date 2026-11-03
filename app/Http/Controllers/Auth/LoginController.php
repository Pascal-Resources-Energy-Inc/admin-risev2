<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\AreaDistributor;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/home';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    // public function login(Request $request)
    // {
    //     $this->validateLogin($request);

    //     if ($this->attemptLogin($request)) {
    //         $user = Auth::user();

    //         if (strtolower($user->role) !== 'admin') {
    //             Auth::logout();

    //             throw ValidationException::withMessages([
    //                 $this->username() => ['The provided credentials are incorrect.'],
    //             ]);
    //         }

    //         return $this->sendLoginResponse($request);
    //     }

    //     return $this->sendFailedLoginResponse($request);
    // }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return $this->errorResponse($request, 'Invalid credentials.');
        }

        $user = Auth::user();

        // role check
        if (!$this->validateSelectedRole($request, $user)) {
            Auth::logout();
            return $this->errorResponse($request, 'Invalid role selected.');
        }

        // inactive check
        if ($this->isUserInactive($user)) {
            Auth::logout();
            return $this->errorResponse($request, 'Your account is inactive.');
        }

        $request->session()->regenerate();

        return $this->successResponse($request, $user);
    }

    protected function validateSelectedRole($request, $user)
    {
        $selectedRole = strtolower(trim($request->selected_role));
        $userRole = strtolower(trim($user->role));

        if (!$selectedRole) {
            return true;
        }

        return $selectedRole === $userRole;
    }

    protected function isUserInactive($user)
    {
        $role = strtolower(trim((string) $user->role));

        // Some disabled partner accounts are explicitly renamed with an
        // "Inactive " role prefix when removed from the active directory.
        if (strpos($role, 'inactive ') === 0) {
            return true;
        }

        if ($role === 'admin') {
            return strtolower(trim((string) $user->getAttribute('status'))) === 'inactive';
        }

        if ($role === 'dealer') {
            $status = optional($user->dealer)->status;
            return strtolower(trim((string) $status)) === 'inactive';
        }

        if ($role === 'client' || $role === 'customer') {
            $status = optional($user->client)->status;
            return strtolower(trim((string) $status)) === 'inactive';
        }

        if (in_array($role, ['area distributor', 'provincial distributor', 'mega dealer'], true)) {
            $partner = AreaDistributor::where('user_id', $user->id)->first();

            // A distributor login must still have an active directory record.
            return !$partner || strtolower(trim((string) $partner->status)) === 'inactive';
        }

        return strtolower(trim((string) $user->getAttribute('status'))) === 'inactive';
    }

    protected function sendFailedLoginResponse(Request $request)
    {
        throw ValidationException::withMessages([
            $this->username() => ['The provided credentials are incorrect.'],
        ]);
    }

    protected function successResponse(Request $request, $user)
    {
        $redirect = $this->redirectByRole($user);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'redirect' => $redirect
            ]);
        }

        return redirect($redirect);
    }

    protected function errorResponse(Request $request, $message)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => $message
            ], 422);
        }

        return back()->withErrors(['email' => $message]);
    }

    protected function redirectByRole($user)
    {
        switch (strtolower($user->role)) {
            case 'admin':
                return '/';
            case 'area distributor':
                return '/ad-dashboard';
            default:
                return '/dashboard';
        }
    }
}
