<?php

namespace App\Http\Controllers;

use App\Models\Annonce;
use App\Models\Commentaire;
use App\Models\Commentaire as ModelsCommentaire;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
/**
 * @OA\Tag(
 *     name="Commentaires",
 *     description="Endpoints for managing comments"
 * )
 */
class CommentaireController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/commentaires",
     *     summary="Get all comments",
     *     tags={"Commentaires"},
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Response(
     *        response=200,
     *        description="Successful operation",
     *     @OA\JsonContent(
     *        type="array",
     *       @OA\Items(ref="#/components/schemas/Commentaire")
     *  )
     *),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function index()
    {
        $user = Auth::user();
        $commentaires = Commentaire::all();
            return response()->json(compact('commentaires'), 200);
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
     *     path="/api/commentaireStore{id}",
     *     summary="Add a new comment to an annonce",
     *     tags={"Commentaires"},
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the annonce",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="commentaire", type="string", example="This is a comment")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Comment added successfully",
     *     ),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function store(Request $request,$id)
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
     * Display the specified resource.
     */
    public function show(Commentaire $commentaire)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Commentaire $commentaire)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Commentaire $commentaire)
    {
        //
    }

    /**
     * @OA\Delete(
     *     path="/api/commentairesDestroy{id}",
     *     summary="Delete a comment",
     *     tags={"Commentaires"},
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the comment",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Comment deleted successfully",
     *     ),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function destroy($id)
    {
        $commentaire = Commentaire::find($id);

        if ($commentaire) {
            $commentaire->delete();
            return response()->json("success','Commentaire supprimée avec success", 200);
        } else {
            return response()->json("Commentaire non trouvée");
        }
    }
}
