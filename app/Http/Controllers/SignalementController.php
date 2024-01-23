<?php

namespace App\Http\Controllers;

use App\Models\Annonce;
use App\Models\Signalement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
/**
 * @OA\Tag(
 *     name="Signalements",
 *     description="Endpoints pour la gestion des signalements"
 * )
 */
class SignalementController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/signalements",
     *     summary="Obtenir tous les signalements",
     *     tags={"Signalements"},
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Response(
     *         response=200,
     *         description="Opération réussie",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Signalement")
     *         )
     *     )
     * )
     */
    public function index()
    {
        $signalements = Signalement::all();
        return response()->json(compact('signalements'), 200);
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
     *     path="/api/signalementStore{id}",
     *     summary="Soumettre un signalement pour une annonce",
     *     tags={"Signalements"},
     *     security={
     *         {"bearerAuth": {}}
     *      },
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de l'annonce signalée",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="description", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Signalement soumis avec succès"
     *     ),
     *     @OA\Response(response=404, description="Annonce non trouvée")
     * )
     */
    public function store($id, Request $request)
    {

        $user = Auth::user();
        $annonce = Annonce::find($id);

        $request->validate([
            'description' => 'required|string',
        ]);

        $signalement = new Signalement([
            'description' => $request->input('description'),
            'user_id' => $user->id,
            'annonce_id' => $annonce->id,
        ]);

        $signalement->save();

        return response()->json('Votre signalement a été pris en compte', 201);
    }


    /**
     * Display the specified resource.
     */
    public function show(Signalement $signalement)
    {
        //
    }

    /**
     * @OA\Get(
     *     path="/api/signalementProprietaire",
     *     summary="Obtenir les signalements liés aux annonces acceptées par l'utilisateur",
     *     tags={"Signalements"},
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Response(
     *         response=200,
     *         description="Opération réussie",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Signalement")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Aucun signalement trouvé")
     * )
     */
    public function signalementProp()
    {
        
        $user = Auth::user();
        
        $annonces = Annonce::where('user_id', $user->id)->where('etat', "accepter")->get();

        $signalements = [];

        foreach ($annonces as $annonce) {
            $signalementsAnnonce = Signalement::where('annonce_id', $annonce->id)->get();  
            $signalements = array_merge($signalements, $signalementsAnnonce->toArray());
        }
        if (empty($signalements)) {
            return response()->json('Aucun signalement');
        }
        return response()->json(compact('signalements'), 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Signalement $signalement)
    {
        //
    }

    /**
     * @OA\Delete(
     *     path="/api/signalementDestroy{id}",
     *     summary="Supprimer un signalement",
     *     tags={"Signalements"},
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID du signalement à supprimer",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Signalement supprimé avec succès"
     *     ),
     *     @OA\Response(response=404, description="Signalement non trouvé")
     * )
     */
    public function destroy($id)
    {
        $signalement = Signalement::find($id);

        if ($signalement) {
            $signalement->delete();
            return response()->json("success','Signalement supprimé avec succès", 200);
        } else {
            return response()->json("Signalement non trouvé");
        }
    }
    
}
