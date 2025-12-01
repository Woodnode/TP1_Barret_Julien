<?php

//https://www.php.net/manual/fr/language.oop5.constants.php
//https://www.w3schools.com/php/php_oop_constants.asp

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Critic;

class CriticController extends Controller
{
    /**
 * @OA\Delete(
 *     path="/api/critics/{critic}",
 *     tags={"Critics"},
 *     summary="Supprimer une critique",
 *     description="Supprime une critique de film",
 *     @OA\Parameter(
 *         name="critic",
 *         in="path",
 *         required=true,
 *         description="ID de la critique à supprimer",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=204,
 *         description="Critique supprimée"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Critique non trouvée"
 *     )
 * )
 */
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
