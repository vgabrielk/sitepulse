<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\ClientService;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    protected $clientService;

    public function __construct(ClientService $clientService)
    {
        $this->clientService = $clientService;
    }

    /**
     * Show login form
     */
    public function showLogin()
    {
        if (Auth::guard('web')->check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        if (Auth::guard('web')->attempt($credentials, $remember)) {
            $request->session()->regenerate();
            
            return redirect()->intended(route('dashboard'))
                ->with('success', 'Login realizado com sucesso!');
        }

        throw ValidationException::withMessages([
            'email' => 'As credenciais fornecidas não conferem com nossos registros.',
        ]);
    }

    /**
     * Show registration form
     */
    public function showRegister()
    {
        if (Auth::guard('web')->check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.register');
    }

    /**
     * Handle registration request
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'company' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'terms' => 'required|accepted',
        ]);

        // Use database transaction to ensure both User and Client are created
        try {
            \DB::beginTransaction();
            
            // Create User for authentication
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);
            \Log::info('User created successfully', ['user_id' => $user->id, 'email' => $user->email]);

            // Create Client for business logic
            $clientData = [
                'name' => $request->name,
                'email' => $request->email,
                'company' => $request->company,
                'phone' => $request->phone,
                'plan' => 'free', // Default to free plan
                'is_active' => true,
            ];

            $clientDTO = $this->clientService->createClient($clientData);
            \Log::info('Client created successfully', ['client_id' => $clientDTO->id, 'email' => $clientDTO->email]);

            \DB::commit();
            
            // Auto login after registration
            Auth::guard('web')->login($user);
            \Log::info('User logged in after registration', ['user_id' => $user->id, 'authenticated' => Auth::guard('web')->check()]);
            
            // Send email verification notification
            $user->sendEmailVerificationNotification();
            
        } catch (\Exception $e) {
            \DB::rollback();
            \Log::error('Error during registration', ['error' => $e->getMessage()]);
            throw $e;
        }

        return redirect()->route('verification.notice')
            ->with('success', 'Conta criada com sucesso! Verifique seu email para continuar.');
    }

    /**
     * Handle logout request
     */
    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Logout realizado com sucesso!');
    }

    /**
     * Show password reset request form
     */
    public function showPasswordReset()
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle password reset request
     */
    public function passwordReset(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        try {
            // Find user by email
            $user = \App\Models\User::where('email', $request->email)->first();
            
            if (!$user) {
                return back()->withErrors(['email' => 'Email não encontrado.']);
            }

            // Send password reset notification
            $user->sendPasswordResetNotification(\Illuminate\Support\Facades\Password::getRepository()->create($user));

            return back()->with('success', 'Instruções de recuperação de senha enviadas para seu email.');
        } catch (\Exception $e) {
            \Log::error('Password reset error', ['error' => $e->getMessage()]);
            return back()->withErrors(['email' => 'Erro ao enviar email. Tente novamente.']);
        }
    }

    /**
     * Show password reset form
     */
    public function showPasswordResetForm($token)
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    /**
     * Handle password reset
     */
    public function passwordResetUpdate(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        try {
            // Find user by email
            $user = \App\Models\User::where('email', $request->email)->first();
            
            if (!$user) {
                return back()->withErrors(['email' => 'Email não encontrado.']);
            }

            // Verify token
            $passwordReset = \Illuminate\Support\Facades\DB::table('password_reset_tokens')
                ->where('email', $request->email)
                ->first();

            if (!$passwordReset) {
                return back()->withErrors(['token' => 'Token inválido ou expirado.']);
            }

            // Check if token matches (the stored token is already hashed)
            if (!\Illuminate\Support\Facades\Hash::check($request->token, $passwordReset->token)) {
                return back()->withErrors(['token' => 'Token inválido ou expirado.']);
            }

            // Check if token is not expired (60 minutes)
            if (now()->diffInMinutes($passwordReset->created_at) > 60) {
                // Delete expired token
                \Illuminate\Support\Facades\DB::table('password_reset_tokens')
                    ->where('email', $request->email)
                    ->delete();
                return back()->withErrors(['token' => 'Token expirado. Solicite um novo link.']);
            }

            // Update password
            $user->password = \Illuminate\Support\Facades\Hash::make($request->password);
            $user->save();

            // Delete password reset token
            \Illuminate\Support\Facades\DB::table('password_reset_tokens')
                ->where('email', $request->email)
                ->delete();

            return redirect()->route('login')
                ->with('success', 'Senha redefinida com sucesso! Faça login com sua nova senha.');
        } catch (\Exception $e) {
            \Log::error('Password reset update error', ['error' => $e->getMessage()]);
            return back()->withErrors(['password' => 'Erro ao redefinir senha. Tente novamente.']);
        }
    }
}
