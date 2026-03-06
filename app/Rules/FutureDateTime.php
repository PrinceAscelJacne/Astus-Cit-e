<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class FutureDateTime implements Rule
{
    /**
     * Détermine si la validation passe.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // Vérifie si la date et l'heure sont dans le futur
        return strtotime($value) > time();
    }

    /**
     * Obtient le message d'erreur de validation.
     *
     * @return string
     */
    public function message()
    {
        return 'La date et l\'heure doivent être dans le futur.';
    }
}
