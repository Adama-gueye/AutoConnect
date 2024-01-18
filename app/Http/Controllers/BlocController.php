<?php

/**
 * @OA\Info(
 *      title="Bloc API",
 *      version="1.0.0",
 *      description="API Documentation for managing Blocs"
 * )
 */
namespace App\Http\Controllers;

use App\Models\Bloc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BlocController extends Controller
{
     /**
     * @OA\Get(
     *     path="/api/blocs",
     *     summary="Get a list of all Blocs",
     *     tags={"Blocs"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="blocs", type="array", @OA\Items(ref="#/components/schemas/Bloc"))
     *         )
     *     ),
     * )
     */
    public function index()
    {
        $blocs = Bloc::all();
        return response()->json(compact('blocs'), 200);
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
     *     path="/api/blocStore",
     *     summary="Create a new Bloc",
     *     tags={"Blocs"},
     *     security={"bearerAuth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="image", type="string"),
     *             @OA\Property(property="titre", type="string"),
     *             @OA\Property(property="description", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Bloc added successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     * )
     */
        public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|string',
            'titre' => 'required|string',
            'description' => 'required|string',
        ]);

        $user = Auth::user();
        $bloc = new Bloc([
            'image' => $request->input('image'),
            'titre' => $request->input('titre'),
            'description' => $request->input('description'),
            'user_id' => $user->id,
        ]);

        $bloc->save();

        return response()->json('Bloc ajouté avec succès', 201);
    }
    /**
     * @OA\Put(
     *     path="/api/blocUpdate{id}",
     *     summary="Update an existing Bloc",
     *     tags={"Blocs"},
     *     security={"bearerAuth"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the Bloc to update",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="image", type="string"),
     *             @OA\Property(property="titre", type="string"),
     *             @OA\Property(property="description", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Bloc updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=404, description="Bloc not found"),
     *     @OA\Response(response=403, description="Forbidden")
     * )
     */

    public function update(Request $request, $id)
    {
        $request->validate([
            'image' => 'required|string',
            'titre' => 'required|string',
            'description' => 'required|string',
        ]);

        $user = Auth::user();
        $bloc = Bloc::find($id);

        if (!$bloc) {
            return response()->json('Bloc non trouvé', 404);
        }

        if ($user->id !== $bloc->user_id) {
            return response()->json('Vous n\'avez pas l\'autorisation de mettre à jour ce bloc', 403);
        }

        $bloc->image = $request->input('image');
        $bloc->titre = $request->input('titre');
        $bloc->description = $request->input('description');
        $bloc->save();

        return response()->json('Bloc mis à jour avec succès', 200);
    }


    
    /**
     * @OA\Get(
     *     path="/api/blocShow{id}",
     *     summary="Get details of a specific Bloc",
     *     tags={"Blocs"},
     *     security={"bearerAuth"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the Bloc to retrieve",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="blocs", type="array", @OA\Items(ref="#/components/schemas/Bloc"))
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=404, description="Bloc not found")
     * )
     */
    public function show($id)
    {
        $blocs = Bloc::find($id);
        return response()->json(compact('blocs'), 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Bloc $bloc)
    {
        //
    }

    /**
     * @OA\Delete(
     *     path="/api/blocDestroy{id}",
     *     summary="Delete a specific Bloc",
     *     tags={"Blocs"},
     *     security={"bearerAuth"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the Bloc to delete",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successfully deleted",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=404, description="Bloc not found"),
     *     @OA\Response(response=403, description="Forbidden")
     * )
     */
    public function destroy($id)
    {
        $bloc = Bloc::find($id);

        if ($bloc) {
            $bloc->delete();
            return response()->json("success','Bloc supprimée avec success", 200);
        } else {
            return response()->json("Bloc non trouvée");
        }
    }
}
