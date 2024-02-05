<?php

/**
 * @OA\Info(
 *      title="API Bloc",
 *      version="1.0.0",
 *      description="Documentation de l'API pour la gestion des Blocs"
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
     *     summary="Obtenir une liste de tous les blocs",
     *     tags={"Blocs"},
     *     @OA\Response(
     *         response=200,
     *         description="Opération réussie",
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
     * Affiche le formulaire de création d'une nouvelle ressource.
     */
    public function create()
    {
        //
    }

    /**
     * @OA\Post(
     *     path="/api/blocStore",
     *     summary="Créer un nouveau bloc",
     *     tags={"Blocs"},
     *     security={
     *         {"bearerAuth": {}}
     *     },
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
     *         description="Bloc ajouté avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     * )
     */
    public function store(Request $request)
    {
        // $request->validate([
        //     'image' => 'required|string',
        //     'titre' => 'required|string',
        //     'description' => 'required|string',
        // ]);

        $bloc = new Bloc();
        if($request->file('image')){
            $file= $request->file('image');
            $filename= date('YmdHi').$file->getClientOriginalName();
            $file-> move(public_path('images'), $filename);
            $bloc['image']= $filename;
        }
        $bloc->titre = $request->input('titre');
        $bloc->description = $request->input('description');
        $bloc->save();

        return response()->json('Bloc ajouté avec succès', 201);
    }

    /**
     * @OA\Patch(
     *     path="/api/blocUpdate{id}",
     *     summary="Mettre à jour un bloc existant",
     *     tags={"Blocs"},
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID du bloc à mettre à jour",
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
     *         description="Bloc mis à jour avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Non autorisé"),
     *     @OA\Response(response=404, description="Bloc non trouvé"),
     *     @OA\Response(response=403, description="Interdit")
     * )
     */
    public function update(Request $request, $id)
    {
    $user = Auth::user();
    $bloc = Bloc::find($id);

    if (!$bloc) {
        return response()->json('Bloc non trouvé', 404);
    }

    if ($request->file('image')) {
        if ($bloc->image) {
            $oldImagePath = public_path('images/' . $bloc->image);
            if (file_exists($oldImagePath)) {
                unlink($oldImagePath);
            }
        }

        $file = $request->file('image');
        $filename = date('YmdHi') . $file->getClientOriginalName();
        $file->move(public_path('images'), $filename);
        $bloc->image = $filename;
    }

    $bloc->titre = $request->input('titre');
    $bloc->description = $request->input('description');
    $bloc->save();

    return response()->json('Bloc mis à jour avec succès', 200);
    }

    /**
     * @OA\Get(
     *     path="/api/blocShow{id}",
     *     summary="Obtenir les détails d'un bloc spécifique",
     *     tags={"Blocs"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID du bloc à récupérer",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Opération réussie",
     *         @OA\JsonContent(
     *             @OA\Property(property="blocs", type="array", @OA\Items(ref="#/components/schemas/Bloc"))
     *         )
     *     ),
     *     @OA\Response(response=401, description="Non autorisé"),
     *     @OA\Response(response=404, description="Bloc non trouvé")
     * )
     */
    public function show($id)
    {
        $blocs = Bloc::find($id);
        return response()->json(compact('blocs'), 200);
    }

    /**
     * Affiche le formulaire d'édition de la ressource spécifiée.
     */
    public function edit(Bloc $bloc)
    {
        //
    }

    /**
     * @OA\Delete(
     *     path="/api/blocDestroy{id}",
     *     summary="Supprimer un bloc spécifique",
     *     tags={"Blocs"},
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID du bloc à supprimer",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Supprimé avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Non autorisé"),
     *     @OA\Response(response=404, description="Bloc non trouvé"),
     *     @OA\Response(response=403, description="Interdit")
     * )
     */
    public function destroy($id)
    {
        $bloc = Bloc::find($id);

        if ($bloc) {
            if ($bloc->image) {
                $imagePath = public_path('images/' . $bloc->image);
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }

            $bloc->delete();
            return response()->json("Bloc supprimé avec succès", 200);
        } else {
            return response()->json("Bloc non trouvé", 404);
        }
    }

}
