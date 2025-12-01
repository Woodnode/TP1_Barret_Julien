<?php

//https://www.php.net/manual/fr/language.oop5.constants.php
//https://www.w3schools.com/php/php_oop_constants.asp
//https://laravel.com/docs/12.x/hashing
//https://laravel.com/docs/12.x/routing#route-model-binding
//https://laravel.com/docs/12.x/queries#joins

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

    /**
 * @OA\Post(
 *     path="/api/users",
 *     tags={"Users"},
 *     summary="Créer un utilisateur",
 *     description="Crée un nouvel utilisateur",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 required={"login","password","email","last_name","first_name"},
 *                 @OA\Property(property="login", type="string", maxLength=50, example="johndoe"),
 *                 @OA\Property(property="password", type="string", minLength=8, example="password123"),
 *                 @OA\Property(property="email", type="string", format="email", example="john@example.com"),
 *                 @OA\Property(property="last_name", type="string", maxLength=50, example="Doe"),
 *                 @OA\Property(property="first_name", type="string", maxLength=50, example="John")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Utilisateur créé"
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Erreur de validation"
 *     )
 * )
 */
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

    /**
 * @OA\Put(
 *     path="/api/users/{user}",
 *     tags={"Users"},
 *     summary="Mettre à jour un utilisateur (complètement)",
 *     @OA\Parameter(
 *         name="user",
 *         in="path",
 *         required=true,
 *         description="ID de l'utilisateur à mettre à jour",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 required={"login","password","email","last_name","first_name"},
 *                 @OA\Property(property="login", type="string", maxLength=50, example="updatedlogin"),
 *                 @OA\Property(property="password", type="string", minLength=8, example="newpassword123"),
 *                 @OA\Property(property="email", type="string", format="email", example="updated@example.com"),
 *                 @OA\Property(property="last_name", type="string", maxLength=50, example="Updated"),
 *                 @OA\Property(property="first_name", type="string", maxLength=50, example="User")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Utilisateur mis à jour"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Utilisateur non trouvé"
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Erreur de validation"
 *     )
 * )
 */
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

    /**
 * @OA\Get(
 *     path="/api/users/{user}/preferred-language",
 *     tags={"Users"},
 *     summary="Recevoir l'information du langage préféré d'un utilisateur",
 *     description="Détermine le langage préféré selon les critiques de films de l'utilisateur",
 *     @OA\Parameter(
 *         name="user",
 *         in="path",
 *         required=true,
 *         description="ID de l'utilisateur",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Langage préféré retourné avec succès"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Aucune critique trouvée pour cet utilisateur"
 *     )
 * )
 */
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
