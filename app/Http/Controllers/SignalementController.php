<?php

namespace App\Http\Controllers;

use App\Models\Annonce;
use App\Models\Signalement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
/**
 * @OA\Tag(
 *     name="Signalements",
 *     description="Endpoints for managing signalements"
 * )
 */
class SignalementController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/signalements",
     *     summary="Get all signalements",
     *     tags={"Signalements"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
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
     *     path="/api/signalementStore/{id}",
     *     summary="Submit signalement for an annonce",
     *     tags={"Signalements"},
     *     security={
     *         {"bearerAuth": {}}
     *      },
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the annonce being reported",
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
     *         description="Signalement submitted successfully"
     *     ),
     *     @OA\Response(response=404, description="Annonce not found")
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
     *     summary="Get signalements related to user's accepted annonces",
     *     tags={"Signalements"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Signalement")
     *         )
     *     ),
     *     @OA\Response(response=404, description="No signalements found")
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
     *     summary="Delete a signalement",
     *     tags={"Signalements"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the signalement to be deleted",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Signalement deleted successfully"
     *     ),
     *     @OA\Response(response=404, description="Signalement not found")
     * )
     */
    public function destroy($id)
    {
        $signalement = Signalement::find($id);

        if ($signalement) {
            $signalement->delete();
            return response()->json("success','Signalement supprimée avec success", 200);
        } else {
            return response()->json("Signalement non trouvée");
        }
    }
    
}
