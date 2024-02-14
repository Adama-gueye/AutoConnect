<?php

namespace App\Http\Controllers;

use App\Models\Annonce;
use App\Models\Commentaire;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Tag(
 *     name="Commentaires",
 *     description="Endpoints pour la gestion des commentaire"
 * )
 */
class CommentaireController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/commentaires",
     *     summary="Obtenir tous les commentaires",
     *     tags={"Commentaires"},
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Response(
     *        response=200,
     *        description="Opération réussie",
     *     @OA\JsonContent(
     *        type="array",
     *       @OA\Items(ref="#/components/schemas/Commentaire")
     *  )
     *),
     *     @OA\Response(response=401, description="Non autorisé")
     * )
     */
    public function index()
    {
        $user = Auth::user();
        $commentaires = Commentaire::all();
        return response()->json(compact('commentaires'), 200);
    }

    /**
     * Afficher le formulaire de création d'une nouvelle ressource.
     */
    public function create()
    {
        //
    }

    /**
     * @OA\Post(
     *     path="/api/commentaireStore{id}",
     *     summary="Ajouter un nouveau commentaire à une annonce",
     *     tags={"Commentaires"},
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de l'annonce",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="commentaire", type="string", example="Ceci est un commentaire")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Commentaire ajouté avec succès",
     *     ),
     *     @OA\Response(response=401, description="Non autorisé")
     * )
     */
    public function store(Request $request, $id)
    {
        $request->validate([
            'commentaire' => 'required',
        ]);

        $user = Auth::user();
        $annonce = Annonce::find($id);
        $commentaire = new Commentaire([
            'commentaire' => $request->input('commentaire'),
            'user_id' => $user->id,
            'annonce_id' => $annonce->id,
        ]);

        $commentaire->save();

        return response()->json('Commentaire ajouté avec succès', 201);
    }

    /**
     * Afficher la ressource spécifiée.
     */
    public function show(Commentaire $commentaire)
    {
        //
    }

    /**
     * Afficher le formulaire d'édition de la ressource spécifiée.
     */
    public function edit(Commentaire $commentaire)
    {
        //
    }

    /**
     * Mettre à jour la ressource spécifiée dans le stockage.
     */
    public function update(Request $request, Commentaire $commentaire)
    {
        //
    }

    /**
     * @OA\Delete(
     *     path="/api/commentaireDestroy{id}",
     *     summary="Supprimer un commentaire",
     *     tags={"Commentaires"},
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID du commentaire",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Commentaire supprimé avec succès",
     *     ),
     *     @OA\Response(response=401, description="Non autorisé")
     * )
     */
    public function destroy($id)
    {
        $commentaire = Commentaire::find($id);

        if ($commentaire) {
            $commentaire->delete();
            return response()->json("success','Commentaire supprimé avec succès", 200);
        } else {
            return response()->json("Commentaire non trouvé");
        }
    }
}
