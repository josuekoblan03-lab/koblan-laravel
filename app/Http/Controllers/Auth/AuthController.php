<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\City;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login', ['title' => 'Connexion - KOBLAN']);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password'], 'is_active' => true])) {
            $request->session()->regenerate();
            
            $user = Auth::user();
            // Log connection
            $user->loginLogs()->create([
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'action' => 'login'
            ]);

            return $this->redirectByRole($user)->with('success', "Bienvenue, {$user->name} ! 🎉");
        }

        return back()->withErrors(['email' => 'Email ou mot de passe incorrect.'])->onlyInput('email');
    }

    public function showRegister()
    {
        $cities = City::with('neighborhoods')->orderBy('name')->get();
        return view('auth.register', [
            'title' => 'Inscription - KOBLAN',
            'cities' => $cities
        ]);
    }

    public function showRegisterPrestataire()
    {
        $cities = City::with('neighborhoods')->orderBy('name')->get();
        return view('auth.register_prestataire', [
            'title' => 'Devenir Prestataire - KOBLAN',
            'cities' => $cities
        ]);
    }

    public function register(Request $request)
    {
        // If the form sends role_principal (unified form), use it, otherwise default to client
        $role = $request->input('role_principal', 'client');
        return $this->processRegister($request, $role);
    }

    public function registerPrestataire(Request $request)
    {
        return $this->processRegister($request, 'prestataire');
    }

    private function processRegister(Request $request, string $role)
    {
        $rules = [
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone' => ['required', 'string', 'max:20'],
            'city_id' => ['required', 'exists:cities,id'],
            'neighborhood_id' => ['nullable', 'exists:neighborhoods,id'],
            'photo' => ['nullable', 'image', 'max:2048'],
            // Add fallback for single 'name' field if they use a different form
            'name' => ['nullable', 'string', 'max:255'], 
            'nom' => ['nullable', 'string', 'max:255'],
            'prenom' => ['nullable', 'string', 'max:255'],
        ];

        $request->validate($rules);

        // Determine full name
        $fullName = '';
        if ($request->filled('nom') || $request->filled('prenom')) {
            $fullName = trim(($request->nom ?? '') . ' ' . ($request->prenom ?? ''));
        } else {
            $fullName = $request->name ?? 'Anonyme';
        }

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('profiles', 'public');
        }

        $user = User::create([
            'name' => $fullName,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $role,
            'phone' => $request->phone,
            'city_id' => $request->city_id,
            'neighborhood_id' => $request->neighborhood_id,
            'avatar' => $photoPath,
            'is_verified' => $role === 'client' ? true : false,
            'is_active' => true,
        ]);

        // Create wallet
        $user->wallet()->create(['balance' => 0]);

        // Welcome notification
        $user->notifications()->create([
            'title' => 'Bienvenue sur KOBLAN ! 🎉',
            'message' => 'Votre compte a été créé avec succès. Explorez nos services dès maintenant.',
            'type' => 'info'
        ]);

        Auth::login($user);

        $msg = $role === 'prestataire' 
            ? 'Compte créé ! Votre profil prestataire est en attente de validation. ⏳'
            : "Bienvenue sur KOBLAN, {$user->name} ! 🎊";

        return $this->redirectByRole($user)->with('success', $msg);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    private function redirectByRole($user)
    {
        if ($user->isAdmin()) return redirect()->route('admin.dashboard');
        if ($user->isPrestataire()) return redirect()->route('prestataire.dashboard');
        return redirect()->route('client.dashboard');
    }
}
