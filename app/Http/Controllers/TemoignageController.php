<?php

namespace App\Http\Controllers;

use App\Models\Temoignage;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TemoignageController extends Controller
{
    /**
     * Dépôt d'un témoignage depuis le site vitrine.
     *
     * Le point d'entrée est public : le statut n'est jamais accepté depuis la
     * requête et reste « en_attente » jusqu'à validation dans le back-office.
     */
    public function store(Request $request)
    {
        $donnees = $request->validate([
            'nom' => 'required|string|max:120',
            'entreprise' => 'nullable|string|max:120',
            'service' => ['required', Rule::in(Temoignage::SERVICES)],
            'note' => 'required|integer|min:1|max:5',
            'message' => 'required|string|min:20|max:500',
        ], [
            'nom.required' => 'Veuillez indiquer votre nom.',
            'service.in' => 'Veuillez choisir un service dans la liste.',
            'note.required' => 'Veuillez attribuer une note.',
            'message.min' => 'Votre témoignage doit faire au moins 20 caractères.',
            'message.max' => 'Votre témoignage ne peut pas dépasser 500 caractères.',
        ]);

        Temoignage::create($donnees);

        return redirect()
            ->back()
            ->with('temoignage_succes', 'Merci ! Votre témoignage a bien été envoyé. Il sera publié après validation par notre équipe.')
            ->withFragment('temoignages');
    }

    /**
     * Liste de modération, réservée au Boss par le middleware de route.
     */
    public function index()
    {
        return view('dashboard.pages.temoignages.index', [
            'enAttente' => Temoignage::enAttente()->latest()->get(),
            'traites' => Temoignage::whereIn('statut', [Temoignage::PUBLIE, Temoignage::REFUSE])
                ->latest()
                ->paginate(15),
        ]);
    }

    public function publier(Temoignage $temoignage)
    {
        // « statut » est hors $fillable pour que le formulaire public ne puisse
        // pas l'imposer : le changement d'état passe donc par forceFill.
        $temoignage->forceFill(['statut' => Temoignage::PUBLIE])->save();

        return redirect()->back()->with('success', 'Témoignage publié sur le site.');
    }

    public function refuser(Temoignage $temoignage)
    {
        $temoignage->forceFill(['statut' => Temoignage::REFUSE])->save();

        return redirect()->back()->with('success', 'Témoignage refusé : il n\'apparaîtra pas sur le site.');
    }

    public function destroy(Temoignage $temoignage)
    {
        $temoignage->delete();

        return redirect()->back()->with('success', 'Témoignage supprimé.');
    }
}
