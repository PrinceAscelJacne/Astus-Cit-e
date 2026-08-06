<?php

namespace App\Http\Controllers;

use App\Mail\MailForm;
use App\Models\Frontrdv;
use App\Models\Frontmessage;
use Illuminate\Http\Request;
use App\Rules\FutureDateTime;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class FrontendController extends Controller
{
    public function sendmail(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|numeric|digits_between:8,15',
            'email' => 'required|email|max:255',
            'subject' => 'nullable|string|max:500',
            'message' => 'nullable|string|max:5000',
        ], [
            'name.max' => 'Le nom renseigné est trop long, veuillez faire plus court.',
            'phone.numeric' => 'Numéro de téléphone invalide.',
            'phone.digits_between' => 'Numéro de téléphone invalide. Veuillez entrer un numéro entre 8 et 15 chiffres.',
            'email.email' => 'Email invalide.',
            'subject.max' => 'Veuillez résumer votre sujet.',
        ]);

        $frontsms = Frontmessage::create([
            'name' => $validatedData['name'],
            'phone' => $validatedData['phone'],
            'email' => $validatedData['email'],
            'subject' => $validatedData['subject'] ?? null,
            'message' => $validatedData['message'] ?? null,
        ]);

        $details = [
            'name' => $frontsms->name,
            'phone' => $frontsms->phone,
            'email' => $frontsms->email,
            'subject' => $frontsms->subject ?? 'no subject',
            'message' => $frontsms->message ?? 'No message',
        ];

        try {
            Mail::to(config('mail.contact_recipient'))->send(new MailForm($details));
        } catch (\Throwable $th) {
            // Le message est journalisé et non plus renvoyé au visiteur :
            // il exposait la configuration SMTP en pleine page.
            Log::error('Envoi du mail de contact impossible', ['exception' => $th]);
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
            'message1' => 'nullable|string|max:5000',
        ], [
            'name1.max' => 'Le nom renseigné est trop long ! Veuillez faire plus court.',
            'email1.email' => "L'email renseigné n'est pas valide.",
            'phone1.digits_between' => 'Numéro de téléphone invalide.',
            'phone1.numeric' => 'Numéro de téléphone invalide.',
            'date1.date' => 'Date invalide.',
        ]);

        try {
            Frontrdv::create([
                'name' => $validatedData['name1'],
                'email' => $validatedData['email1'],
                'phone' => $validatedData['phone1'],
                'date' => $validatedData['date1'],
                'message' => $validatedData['message1'] ?? '',
            ]);
        } catch (\Throwable $e) {
            Log::error('Enregistrement de la demande de rendez-vous impossible', ['exception' => $e]);

            return redirect()->back()->with('error', "Une erreur s'est produite, veuillez réessayer plus tard.");
        }

        return redirect()->back()->with('success', 'Votre demande de rendez-vous a été envoyée avec succès. Merci!');
    }

    public function dashboard()
    {
        $today = now()->startOfDay();
        $tomorrow = now()->addDay()->startOfDay();

        $rendezvous = Frontrdv::orderBy('date', 'asc')->get()->groupBy(function ($rdv) use ($today, $tomorrow) {
            $dateObj = \Carbon\Carbon::parse($rdv->date);

            if ($dateObj->lt($today)) {
                return 'Passé';
            }

            if ($dateObj->lt($tomorrow)) {
                return 'Aujourdhui';
            }

            return 'A venir';
        });

        $messages = Frontmessage::orderBy('created_at', 'desc')->get();

        return view('frontend_dashboard', compact('rendezvous', 'messages'));
    }
}
