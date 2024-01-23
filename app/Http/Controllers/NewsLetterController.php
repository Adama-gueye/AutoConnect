<?php

namespace App\Http\Controllers;

use App\Models\NewsLetter;
use Illuminate\Http\Request;
/**
 * @OA\Tag(
 *     name="Newsletters",
 *     description="Endpoints pour la gestion des newsletters"
 * )
 */
class NewsLetterController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/newsLetters",
     *     summary="Obtenir toutes les newsletters",
     *     tags={"Newsletters"},
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Response(
     *         response=200,
     *         description="Opération réussie",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/NewsLetter")
     *         )
     *     )
     * )
     */
    public function index()
    {
        $newsLetters = NewsLetter::all();
        return response()->json(compact('newsLetters'), 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    
    /**
     * @OA\Post(
     *     path="/api/newsLetterStore",
     *     summary="S'abonner à la newsletter",
     *     tags={"Newsletters"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email"},
     *             @OA\Property(property="email", type="string", format="email", example="exemple@exemple.com")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Inscription réussie"
     *     ),
     *     @OA\Response(response=422, description="Email déjà inscrit"),
     *     @OA\Response(response=400, description="Entrée non valide")
     * )*/
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:news_letters,email',
        ]);
        $existingEmail = NewsLetter::where('email', $request->input('email'))->exists();
    
        if ($existingEmail) {
            return response()->json('Cet email est déjà inscrit à la newsletter', 422);
        }
    
        $newsLetter = new NewsLetter([
            'email' => $request->input('email'),
        ]);
    
        $newsLetter->save();
    
        return response()->json('Inscription à la newsletter réussie', 201);
    }
    

    /**
     * Display the specified resource.
     */
    public function show(NewsLetter $newsLetter)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(NewsLetter $newsLetter)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, NewsLetter $newsLetter)
    {
        //
    }

     /**
     * @OA\Delete(
     *     path="/api/newsLetterDestroy{id}",
     *     summary="Se désabonner de la newsletter",
     *     tags={"Newsletters"},
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de l'abonnement à la newsletter",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Désinscription réussie"
     *     ),
     *     @OA\Response(response=404, description="Abonnement non trouvé")
     * )
     */
    public function destroy($id)
    {
        $newsLetter = NewsLetter::find($id);

        if ($newsLetter) {
            $newsLetter->delete();
            return response()->json("success','Email supprimé avec succès", 200);
        } else {
            return response()->json("Email non trouvé");
        }
    }
}
