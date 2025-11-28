<?php

//https://www.php.net/manual/fr/language.oop5.constants.php
//https://www.w3schools.com/php/php_oop_constants.asp

namespace App\Http\Controllers;
use App\Http\Resources\ActorResource;
use App\Http\Resources\CriticResource;
use App\Http\Resources\FilmResource;
use App\Models\Film;
use Illuminate\Http\Request;

class FilmController extends Controller
{
    
    //  Recevoir l'information de tous les films
    public function index()
    {
        try 
        {
            $films = Film::all();
            return FilmResource::collection($films)
                ->response()
                ->setStatusCode(self::HTTP_OK);
        } 
        catch (\Exception $e) 
        {
            abort(self::HTTP_INTERNAL_SERVER_ERROR, 'Erreur lors de la récupération des films');
        }
    }

    //Recevoir l’information de tous les acteurs d’un film en particulier
    public function actors(Film $film)
    {
        try 
        {
            return ActorResource::collection($film->actors)
                ->response()
                ->setStatusCode(self::HTTP_OK);
        } 
        catch (\Exception $e) 
        {
            abort(self::HTTP_NOT_FOUND, 'Film non trouvé');
        }
    }

    //Recevoir l’information d’un film avec ses critiques
    public function show(Film $film)
    {
        try 
        {
            return response()->json([
                'film' => new FilmResource($film),
                'critics' => CriticResource::collection($film->critics),
            ], self::HTTP_OK);
        } 
        catch (\Exception $e) 
        {
            abort(self::HTTP_NOT_FOUND, 'Film non trouvé');
        }
    }

    //Recevoir la moyenne des scores pour un film en particulier
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

    //Recevoir l’information de films suite à une recherche
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


