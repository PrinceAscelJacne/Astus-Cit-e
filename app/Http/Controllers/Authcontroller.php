<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use App\Models\Session;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class Authcontroller extends Controller
{
    public function register ()
    {
        $dep = Department::all();
        return view('dashboard.pages.authentication.register', compact('dep'));
    }

    public function store(Request $request)
    {
        try {

            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'firstname' => 'required|string|max:255',
                'phone' => 'required|digits:8',
                'email' => 'required|string|email|max:255|unique:users',
                'department_id' => 'nullable|exists:departments,id',
                'role' => 'required|exists:roles,id',
            ]);


            $password = $request->password;
            // Créer l'utilisateur
            $user = User::create([
                'name' => htmlspecialchars(trim($validatedData['name'])),
                'surname' => htmlspecialchars(trim($validatedData['firstname'])),
                'phone' => htmlspecialchars(trim($validatedData['phone'])),
                'email' => htmlspecialchars($validatedData['email']),
                'password' => Hash::make($password),
                'department_id' => $validatedData['department_id'] ?? null ,
                'role_id' => intval($validatedData['role']),
            ]);

            // Retourner une réponse
            return redirect()->back()->with('success', 'Utilisateur'. $request->name. 'ajouté avec succès !');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'L\'email renseigné à dejà été attribué');
        }
    }

    public function edit()
    {
        $user = Auth::user();
        return view('profilex.profileshower', compact('user'));
    }


    public function updateinfos (Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . Auth::id(),
        ],[
            'name.max' => 'le nom renseigné est trop long.',
            'surname.max' => 'le prénom renseigné est trop long.',
            'phone.max' => 'Numéro de téléphone invalide.',
            'email.max' => 'le prénom renseigné est trop long.',
            'email.email' => 'l\email renseigné n\'est pas valide.',
            'email.unique' => 'l\email renseigné à déja été attribué, veuillez renseigner un autre.',
        ]);

        $userid = Auth::user()->id;
        $user = User::findOrFail($userid);

        $user->name = $request->input('name');
        $user->surname = $request->input('surname');
        $user->phone = $request->input('phone');
        $user->email = $request->input('email');
        $user->save();


        return redirect()->route('profileshower')->with('success', 'Profil mis à jour avec succès.');
    }

    public function updatePassword(Request $request)
    {
        // Validate the incoming request fields
        $request->validate([
            'currentpass' => 'required',
            'newpass' => 'required|min:8',
            'newpass_confirmation' => 'required|same:newpass',
        ], [
            'currentpass.required' => 'Veuillez saisir votre mot de passe actuel.',
            'newpass.required' => 'Veuillez saisir un nouveau mot de passe.',
            'newpass.min' => 'Le nouveau mot de passe doit contenir au moins :min caractères.',
            'newpass_confirmation.required' => 'Veuillez confirmer votre nouveau mot de passe.',
            'newpass_confirmation.same' => 'La confirmation du nouveau mot de passe ne correspond pas.',
        ]);

        // Get the authenticated user
        $user = User::findOrFail(Auth::user()->id);

        // Check if the current password matches the one in the database
        if (!Hash::check($request->currentpass, $user->password)) {
            return redirect()->back()->with('error', 'Le mot de passe actuel est incorrect.');
        }

        // Update the user's password
        $user->password = Hash::make($request->newpass);
        $user->save();

        // Redirect back with success message
        return redirect()->back()->with('success', 'Votre mot de passe a été mis à jour avec succès.');
    }


    public function index()
    {
        $sessions = DB::table('sessions')->where('user_id', Auth::id())->get()
        ->map(function ($session) {
            return (new Session)->forceFill((array) $session);
        });;

        return view('profilex.sessions', compact('sessions'));
    }

    public function logoutOtherSessions(Request $request)
    {
        $request->validate([
            'password' => 'required',
        ]);

        if (!Hash::check($request->password, Auth::user()->password)) {
            return back()->withErrors(['password' => 'Le mot de passe actuel ne correspond pas.']);
        }

        DB::table('sessions')->where('user_id', Auth::id())->where('id', '!=', session()->getId())->delete();

        return back()->with('status', 'Déconnecté des autres sessions avec succès.');
    }


    public function deleteAccount(Request $request)
    {
        $user = User::findOrFail(auth()->user()->id);

        if (!password_verify($request->password1, $user->password)) {
            return redirect()->back()->with('error1', 'Le mot de passe est incorrect.');
        }
        DB::beginTransaction();
        try {
            // Récupérer l'utilisateur authentifié


            if (!$user) {
                throw new \Exception('Utilisateur non trouvé.');
            }

            // Soft delete de l'utilisateur
            $user->delete();

            DB::commit();

            // Déconnecter l'utilisateur
            auth()->logout();

            // Rediriger avec un message de succès
            return redirect()->route('login')->with('success', 'Votre compte a été supprimé avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();

            // En cas d'erreur, rediriger avec un message d'erreur
            return redirect()->back()->with('error1', 'Une erreur est survenue lors de la suppression du compte.');
        }
    }

}
