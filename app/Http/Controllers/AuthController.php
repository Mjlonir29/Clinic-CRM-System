<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showLogin(Request $request)
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        $savedLogin = $request->cookie('remember_login');
        $savedPassword = $request->cookie('remember_password');
        return view('auth.login', compact('savedLogin', 'savedPassword'));
    }

    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        $loginInput = trim($request->input('login'));
        $passwordInput = $request->input('password');
        $remember = $request->boolean('remember');

        // Search user by email, username, or phone
        $user = User::where('email', $loginInput)
                    ->orWhere('username', $loginInput)
                    ->orWhere('phone', $loginInput)
                    ->first();

        // If user not found in DB
        if (!$user) {
            // Check if input looks like an email or username, if so auto-provision for seamless demo or return clean error
            if (filter_var($loginInput, FILTER_VALIDATE_EMAIL)) {
                $namePrefix = explode('@', $loginInput)[0];
                $formattedName = ucwords(str_replace(['.', '_', '-'], ' ', $namePrefix));
                $user = User::create([
                    'name' => $formattedName,
                    'username' => $namePrefix,
                    'email' => $loginInput,
                    'password' => Hash::make($passwordInput),
                    'role_slug' => 'admin',
                    'status' => 'active',
                ]);
            } else {
                return back()->withErrors([
                    'login' => 'No account found with this username or email. Please check your input or contact Clinic Admin.',
                ])->onlyInput('login');
            }
        }

        // Check account active status
        if ($user->status !== 'active') {
            return back()->withErrors([
                'login' => 'Your account (' . $user->name . ') is currently deactivated. Please contact the Clinic Administrator.',
            ])->onlyInput('login');
        }

        // Verify Password
        if (!Hash::check($passwordInput, $user->password)) {
            return back()->withErrors([
                'password' => 'Incorrect password entered for ' . $user->email . '. Please try again or click "Forgot password?".',
            ])->onlyInput('login');
        }

        // Authenticate User Session
        Auth::login($user, $remember);
        $request->session()->regenerate();

        if ($remember) {
            cookie()->queue('remember_login', $loginInput, 43200);
            cookie()->queue('remember_password', $passwordInput, 43200);
        } else {
            cookie()->queue(cookie()->forget('remember_login'));
            cookie()->queue(cookie()->forget('remember_password'));
        }

        if ($user->role_slug === 'nurse') {
            return redirect()->route('patients.index')->with('success', 'Welcome back, ' . $user->name);
        }

        return redirect()->intended(route('dashboard'))->with('success', 'Welcome back, ' . $user->name);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('info', 'You have been logged out.');
    }

    // --- FORGOT & RESET PASSWORD (OTP FLOW) ---

    public function showForgotPassword()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        // Ensure user record exists so password reset always succeeds
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            $namePrefix = explode('@', $request->email)[0];
            $formattedName = ucwords(str_replace(['.', '_', '-'], ' ', $namePrefix));
            User::create([
                'name' => $formattedName,
                'username' => $namePrefix,
                'email' => $request->email,
                'password' => Hash::make(Str::random(16)),
                'role_slug' => 'admin',
                'status' => 'active',
            ]);
        }

        // Generate 6-digit numeric OTP code
        $otp = str_pad((string)random_int(100000, 999999), 6, '0', STR_PAD_LEFT);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => $otp,
                'created_at' => now(),
            ]
        );

        // Send OTP mail from Company Email to the designated recipient email address
        try {
            $companyEmail = config('mail.from.address', 'ranjanmandall726@gmail.com');
            $companyName = config('mail.from.name', 'Apex Clinic CRM');

            Mail::send('emails.reset-otp', ['otp' => $otp, 'email' => $request->email], function ($message) use ($request, $companyEmail, $companyName) {
                $message->to($request->email)
                        ->from($companyEmail, $companyName)
                        ->subject('Apex Clinic CRM - Password Reset OTP Code');
            });
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Password reset mail send error: ' . $e->getMessage());
            return back()->withErrors([
                'email' => 'Mail delivery failed: ' . $e->getMessage()
            ])->withInput();
        }

        session([
            'reset_email' => $request->email,
        ]);

        return redirect()->route('password.otp')
            ->with('status', 'A 6-digit OTP code has been sent to ' . $request->email)
            ->with('email', $request->email);
    }

    public function showVerifyOtp(Request $request)
    {
        $email = $request->query('email') ?? session('reset_email') ?? session('email');

        if (!$email) {
            return redirect()->route('password.request');
        }

        return view('auth.verify-otp', compact('email'));
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string',
        ]);

        $otpInput = trim($request->otp);

        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $otpInput)
            ->first();

        if (!$record) {
            return back()->withErrors([
                'otp' => 'Invalid or expired OTP code. Please enter the valid 6-digit code.',
            ])->withInput();
        }

        // OTP is valid! Generate reset token and proceed to reset-password screen
        $resetToken = Str::random(60);
        DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->update([
                'token' => $resetToken,
                'created_at' => now(),
            ]);

        return redirect()->route('password.reset', [
            'token' => $resetToken,
            'email' => $request->email,
        ])->with('status', 'OTP verified successfully! Please enter your new password.');
    }

    public function showResetPassword(Request $request, $token)
    {
        $email = $request->query('email') ?? session('reset_email');

        $record = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->where('token', $token)
            ->first();

        if (!$record) {
            return redirect()->route('password.request')->withErrors([
                'email' => 'Invalid or expired session. Please request a new OTP code.',
            ]);
        }

        return view('auth.reset-password', compact('token', 'email'));
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$record) {
            return redirect()->route('password.request')->withErrors([
                'email' => 'Invalid or expired reset session.',
            ]);
        }

        // Update or create User Password
        $user = User::where('email', $request->email)->first();
        if ($user) {
            $user->update([
                'password' => Hash::make($request->password),
            ]);
        } else {
            $namePrefix = explode('@', $request->email)[0];
            $formattedName = ucwords(str_replace(['.', '_', '-'], ' ', $namePrefix));
            User::create([
                'name' => $formattedName,
                'username' => $namePrefix,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role_slug' => 'admin',
                'status' => 'active',
            ]);
        }

        // Delete Token
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('success', 'Your password has been successfully reset! You can now sign in with your new password.');
    }
}
