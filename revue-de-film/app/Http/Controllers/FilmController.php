<?php

//https://www.php.net/manual/fr/language.oop5.constants.php
//https://www.w3schools.com/php/php_oop_constants.asp
//https://laravel.com/docs/12.x/responses#json-responses
//https://laravel.com/docs/12.x/routing#route-model-binding



namespace App\Http\Controllers;
use App\Http\Resources\ActorResource;
use App\Http\Resources\CriticResource;
use App\Http\Resources\FilmResource;
use App\Models\Film;
use Illuminate\Http\Request;

class FilmController extends Controller
{
    
    /**
 * @OA\Get(
 *     path="/api/films",
 *     tags={"Films"},
 *     summary="Recevoir l'information de tous les films",
 *     description="Retourne une liste paginée de films",
 *     @OA\Response(
 *         response=200,
 *         description="Liste de films retournée avec succès"
 *     )
 * )
 */
    public function index()
    {
        try 
        {
            $films = Film::paginate(20);
            return FilmResource::collection($films)
                ->response()
                ->setStatusCode(self::HTTP_OK);
        } 
        catch (\Exception $e) 
        {
            abort(self::HTTP_INTERNAL_SERVER_ERROR, 'Erreur lors de la récupération des films');
        }
    }

    /**
 * @OA\Get(
 *     path="/api/films/{film}/actors",
 *     tags={"Films"},
 *     summary="Recevoir l'information de tous les acteurs d'un film en particulier",
 *     @OA\Parameter(
 *         name="film",
 *         in="path",
 *         required=true,
 *         description="ID du film",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Liste des acteurs du film"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Film non trouvé"
 *     )
 * )
 */
    public function actors(Film $film)
    {
        try 
        {
            return ActorResource::collection($film->actors()->paginate(20))
                ->response()
                ->setStatusCode(self::HTTP_OK);
        } 
        catch (\Exception $e) 
        {
            abort(self::HTTP_NOT_FOUND, 'Film non trouvé');
        }
    }

    /**
 * @OA\Get(
 *     path="/api/films/{film}",
 *     tags={"Films"},
 *     summary="Recevoir l'information d'un film avec ses critiques",
 *     @OA\Parameter(
 *         name="film",
 *         in="path",
 *         required=true,
 *         description="ID du film",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Film et ses critiques retournés avec succès"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Film non trouvé"
 *     )
 * )
 */
    public function show(Film $film)
    {
        try 
        {
            return response()->json([
                'film' => new FilmResource($film),
                'critics' => CriticResource::collection($film->critics()->paginate(20))->response()->getData(true),
            ], self::HTTP_OK);
        } 
        catch (\Exception $e) 
        {
            abort(self::HTTP_NOT_FOUND, 'Film non trouvé');
        }
    }

    /**
 * @OA\Get(
 *     path="/api/films/{film}/average-score",
 *     tags={"Films"},
 *     summary="Recevoir la moyenne des scores pour un film en particulier",
 *     @OA\Parameter(
 *         name="film",
 *         in="path",
 *         required=true,
 *         description="ID du film",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Moyenne des scores retournée avec succès"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Film non trouvé"
 *     )
 * )
 */
    public function averageScore(Film $film)
    {
        try 
        {
            $averageScore = $film->critics()->avg('score');
            return response()->json([
                'film_id' => $film->id,
                'average_score' => $averageScore ? round($averageScore, 2) : null,
            ], self::HTTP_OK);
        } 
        catch (\Exception $e) 
        {
            abort(self::HTTP_NOT_FOUND, 'Film non trouvé');
        }
    }

    /**
 * @OA\Get(
 *     path="/api/films/search",
 *     tags={"Films"},
 *     summary="Recevoir l'information de films suite à une recherche",
 *     description="Recherche multi-critères sur les films",
 *     @OA\Parameter(
 *         name="keyword",
 *         in="query",
 *         required=false,
 *         description="Filtre les films dont le titre contient ce mot-clé",
 *         @OA\Schema(type="string", example="Bri")
 *     ),
 *     @OA\Parameter(
 *         name="rating",
 *         in="query",
 *         required=false,
 *         description="Filtre les films par rating",
 *         @OA\Schema(type="string", example="PG")
 *     ),
 *     @OA\Parameter(
 *         name="minLength",
 *         in="query",
 *         required=false,
 *         description="Durée minimale du film (en minutes)",
 *         @OA\Schema(type="integer", example=60)
 *     ),
 *     @OA\Parameter(
 *         name="maxLength",
 *         in="query",
 *         required=false,
 *         description="Durée maximale du film (en minutes)",
 *         @OA\Schema(type="integer", example=90)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Résultats paginés de la recherche"
 *     )
 * )
 */
    public function search(Request $request)
    {
        try 
        {
            $query = Film::query();

            if ($request->has('keyword') && $request->keyword) {
                $query->where('title', 'like', '%' . $request->keyword . '%');
            }

            if ($request->has('rating') && $request->rating) {
                $query->where('rating', $request->rating);
            }

            if ($request->has('minLength') && $request->minLength) {
                $query->where('length', '>=', $request->minLength);
            }

            if ($request->has('maxLength') && $request->maxLength) {
                $query->where('length', '<=', $request->maxLength);
            }

            $films = $query->paginate(20);
            return FilmResource::collection($films)
                ->response()
                ->setStatusCode(self::HTTP_OK);
        } 
        catch (\Exception $e) 
        {
            abort(self::HTTP_INTERNAL_SERVER_ERROR, 'Erreur lors de la recherche');
        }
    }
}


