<?php

namespace App\Http\Controllers;

use App\Mail\MailForm;
use App\Models\Frontrdv;
use App\Models\Frontmessage;
use Illuminate\Http\Request;
use App\Rules\FutureDateTime;
use Illuminate\Support\Facades\Mail;

class FrontendController extends Controller
{
    public function sendmail(Request $request)
    {

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|numeric|digits_between:8,15',
            'email' => 'required|email',
            'subject' => 'nullable|string|max:500',
            'message' => 'nullable|string',
        ], [
            'name.max' => 'Le nom renseigné est trop , Veuillez faire plus court.',
            'phone.numeric' => 'Numéro de téléphone invalide.',
            'phone.digits_between' => 'Numéro de téléphone invalide. Vueillez entrer un numéro valide entre entre 8 et 15 chiffres',
            'email.email' => 'Email invalide',
            'subject.max' => 'Veuillez résumer votre sujet',

        ]);

        $details = [
            'name' => $validatedData['name'],
            'phone' => $validatedData['phone'],
            'email' => $validatedData['email'],
            'subject' => $validatedData['subject'] ?? 'no subject',
            'message' => $validatedData['message'] ?? 'No message',
        ];

        $frontsms = new Frontmessage();

        $frontsms->name = $validatedData['name'];
        $frontsms->phone = $validatedData['phone'];
        $frontsms->email = $validatedData['email'];
        $frontsms->subject = $validatedData['subject'];
        $frontsms->message = $validatedData['message'];
        $frontsms->save();
        try {
            Mail::to('sladedeathstroke79@gmail.com')->send(new MailForm($details));
        } catch (\Throwable $th) {
            echo $th->getMessage();
        }


        return redirect()->back()->with('success', 'Votre message a été envoyé avec succès!');
    }


    public function storerdv(Request $request)
    {
        $validatedData = $request->validate([
            'name1' => 'required|string|max:255',
            'email1' => 'required|email|max:255',
            'phone1' => 'required|numeric|digits_between:8,15',
            'date1' => ['required', 'date', new FutureDateTime],
            'message1' => 'nullable|string',
        ],[
            'name1.max' => 'le nom renseigné est trop long ! Veuillez faire plus court.',
            'email1.email' => 'l\'email renseigné n\'est pas valide.',
            'phone1.digits_between' => 'Numéro de téléphone invalide.',
            'phone1.numeric' => 'Numéro de téléphone invalide.',
            'date1.date' => 'Date invalide.',
            'date1.future_date_time' =>  'La date et l\'heure doivent être dans le futur.',
        ]);


        $frontrdv = new Frontrdv();
        $frontrdv->name = $validatedData['name1'];
        $frontrdv->email = $validatedData['email1']; // Associe le champ email à la colonne mailer
        $frontrdv->phone = $validatedData['phone1'];
        $frontrdv->date = $validatedData['date1'];
        $frontrdv->message = $validatedData['message1'] ?? '';

        // Enregistrer l'objet dans la base de données
        try {
            $frontrdv->save();
            return redirect()->back()->with('success', 'Votre demande de rendez-vous a été envoyée avec succès. Merci!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Une erreur s\'est produite : ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Votre demande de rendez-vous a été envoyée avec succès. Merci!');
    }



    public function dashboard()
    {
        $today = now()->startOfDay();
        $tomorrow = now()->addDay()->startOfDay();

        // Récupérer les rendez-vous triés par date
        $rendezvous = Frontrdv::orderBy('date', 'asc')->get()->groupBy(function($date) use ($today, $tomorrow) {
            $dateObj = \Carbon\Carbon::parse($date->date); // Convertir la date en objet Carbon ou DateTime si ce n'est pas déjà le cas

            if ($dateObj->lt($today)) {
                return 'Passé';
            } elseif ($dateObj->gte($today) && $dateObj->lt($tomorrow)) {
                return 'Aujourdhui';
            } else {
                return 'A venir';
            }
        });

        // Récupérer les messages
        $messages = Frontmessage::orderBy('created_at', 'desc')->get();

        return view('frontend_dashboard', compact('rendezvous', 'messages'));
    }
}
