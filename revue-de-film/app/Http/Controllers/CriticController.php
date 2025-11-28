<?php

//https://www.php.net/manual/fr/language.oop5.constants.php
//https://www.w3schools.com/php/php_oop_constants.asp

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Critic;

class CriticController extends Controller
{
    // Supprimer une critique
    public function destroy(Critic $critic)
    {
        try 
        {
            $critic->delete();
            return response()->json(null, self::HTTP_NO_CONTENT);
        } 
        catch (\Exception $e) 
        {
            abort(self::HTTP_NOT_FOUND, 'Critique non trouvée');
        }
    }
}
