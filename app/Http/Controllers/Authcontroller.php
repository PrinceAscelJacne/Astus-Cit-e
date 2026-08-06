<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Models\Session;
use App\Models\Department;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class Authcontroller extends Controller
{
    public function register()
    {
        $dep = Department::all();

        return view('dashboard.pages.authentication.register', compact('dep'));
    }

    public function store(Request $request)
    {
        $auteur = Auth::user();
        $rolesAutorises = $auteur->rolesAttribuables();

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'firstname' => 'required|string|max:255',
            'phone' => 'required|digits:8',
            'email' => 'required|string|email|max:255|unique:users,email',
            'department_id' => 'nullable|exists:departments,id',
            // Le rôle demandé est confronté à ce que l'auteur a le droit
            // d'attribuer : un chef de département ne peut créer qu'un employé.
            // Sans cette règle, n'importe quel compte pouvait se fabriquer un Boss.
            'role' => ['required', Rule::in($rolesAutorises)],
        ], [
            'email.unique' => "L'email renseigné a déjà été attribué.",
            'phone.digits' => 'Le numéro de téléphone doit comporter 8 chiffres.',
            'role.in' => "Vous n'avez pas le droit d'attribuer ce rôle.",
        ]);

        // Un chef de département rattache forcément à son propre département.
        $departmentId = $auteur->isChefDepartement()
            ? $auteur->department_id
            : ($validatedData['department_id'] ?? null);

        // Le mot de passe est généré ici, et non plus dans la vue : le champ
        // était seulement « readonly », donc librement modifiable côté client.
        $motDePasse = Str::password(12, true, true, false);

        $utilisateur = User::create([
            'name' => trim($validatedData['name']),
            'surname' => trim($validatedData['firstname']),
            'phone' => trim($validatedData['phone']),
            'email' => $validatedData['email'],
            'password' => Hash::make($motDePasse),
            'department_id' => $departmentId,
            'role_id' => (int) $validatedData['role'],
        ]);

        // Le compte est créé par un responsable, qui en atteste et transmet
        // lui-même le mot de passe : il n'y a pas d'auto-inscription à
        // confirmer. Sans cette ligne, email_verified_at restait nul et tous
        // les comptes se seraient retrouvés bloqués le jour où la vérification
        // d'e-mail serait activée, sans moyen de la passer.
        $utilisateur->forceFill(['email_verified_at' => now()])->save();

        return redirect()->back()
            ->with('success', 'Utilisateur ' . $validatedData['name'] . ' ajouté avec succès !')
            ->with('generated_password', $motDePasse);
    }

    public function edit()
    {
        $user = Auth::user();

        return view('profilex.profileshower', compact('user'));
    }

    public function updateinfos(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . Auth::id(),
        ], [
            'name.max' => 'Le nom renseigné est trop long.',
            'surname.max' => 'Le prénom renseigné est trop long.',
            'phone.max' => 'Numéro de téléphone invalide.',
            'email.max' => "L'email renseigné est trop long.",
            'email.email' => "L'email renseigné n'est pas valide.",
            'email.unique' => "L'email renseigné a déjà été attribué, veuillez en renseigner un autre.",
        ]);

        $user = Auth::user();
        $user->name = $request->input('name');
        $user->surname = $request->input('surname');
        $user->phone = $request->input('phone');
        $user->email = $request->input('email');
        $user->save();

        return redirect()->route('profileshower')->with('success', 'Profil mis à jour avec succès.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'currentpass' => 'required',
            'newpass' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::default()],
        ], [
            'currentpass.required' => 'Veuillez saisir votre mot de passe actuel.',
            'newpass.required' => 'Veuillez saisir un nouveau mot de passe.',
            'newpass.confirmed' => 'La confirmation du nouveau mot de passe ne correspond pas.',
        ]);

        $user = Auth::user();

        if (! Hash::check($request->currentpass, $user->password)) {
            return redirect()->back()->with('error', 'Le mot de passe actuel est incorrect.');
        }

        $user->password = Hash::make($request->newpass);
        $user->save();

        return redirect()->back()->with('success', 'Votre mot de passe a été mis à jour avec succès.');
    }

    public function index()
    {
        $sessions = DB::table('sessions')
            ->where('user_id', Auth::id())
            ->get()
            ->map(fn ($session) => (new Session)->forceFill((array) $session));

        return view('profilex.sessions', compact('sessions'));
    }

    public function logoutOtherSessions(Request $request)
    {
        $request->validate([
            'password' => 'required',
        ]);

        if (! Hash::check($request->password, Auth::user()->password)) {
            return back()->withErrors(['password' => 'Le mot de passe actuel ne correspond pas.']);
        }

        DB::table('sessions')
            ->where('user_id', Auth::id())
            ->where('id', '!=', session()->getId())
            ->delete();

        return back()->with('status', 'Déconnecté des autres sessions avec succès.');
    }

    public function deleteAccount(Request $request)
    {
        $request->validate(['password1' => 'required']);

        $user = Auth::user();

        if (! Hash::check($request->password1, $user->password)) {
            return redirect()->back()->with('error1', 'Le mot de passe est incorrect.');
        }

        $user->delete();
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Votre compte a été supprimé avec succès.');
    }
}
