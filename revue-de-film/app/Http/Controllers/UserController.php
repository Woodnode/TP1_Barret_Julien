<?php

//https://www.php.net/manual/fr/language.oop5.constants.php
//https://www.w3schools.com/php/php_oop_constants.asp

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;

class UserController extends Controller
{

    //Créer un utilisateur
    public function store(StoreUserRequest $request)
    {
        try 
        {
            $user = User::create([
            'login' => $request->login,
            'password' => Hash::make($request->password),
            'email' => $request->email,
            'last_name' => $request->last_name,
            'first_name' => $request->first_name,
        ]);

            return (new UserResource($user))
                ->response()
                ->setStatusCode(self::HTTP_CREATED);
        } 
        catch (\Exception $e) 
        {
            abort(self::HTTP_INTERNAL_SERVER_ERROR, 'Erreur lors de la création de l\'utilisateur');
        }
    }

    // Mettre à jour un utilisateur (mise à jour complète et non partielle)
    public function update(UpdateUserRequest $request, User $user)
    {
        try 
        {
            $user->update([
                'login' => $request->login,
                'password' => Hash::make($request->password),
                'email' => $request->email,
                'last_name' => $request->last_name,
                'first_name' => $request->first_name,
            ]);

            return (new UserResource($user))
                ->response()
                ->setStatusCode(self::HTTP_OK);
        } 
        catch (\Exception $e) 
        {
            abort(self::HTTP_NOT_FOUND, 'Utilisateur non trouvé');
        }
    }

    // Recevoir l’information du langage préféré d’un utilisateur
    public function preferredLanguage(User $user)
    {
        try 
        {
            $language = DB::table('critics')
                ->join('films', 'critics.film_id', '=', 'films.id')
                ->join('languages', 'films.language_id', '=', 'languages.id')
                ->where('critics.user_id', $user->id)
                ->select('languages.id', 'languages.name', DB::raw('COUNT(*) as count'))
                ->groupBy('languages.id', 'languages.name')
                ->orderByDesc('count')
                ->first();

            if (!$language) {
                return response()->json([
                    'message' => 'Aucune critique trouvée pour cet utilisateur'
                ], self::HTTP_NOT_FOUND);
            }

            return response()->json([
                'user_id' => $user->id,
                'preferred_language' => [
                    'id' => $language->id,
                    'name' => $language->name,
                ],
            ], self::HTTP_OK);
        } 
        catch (\Exception $e) 
        {
            abort(self::HTTP_INTERNAL_SERVER_ERROR, 'Erreur lors de la récupération du langage préféré');
        }
    }
}
