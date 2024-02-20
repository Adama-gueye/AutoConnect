<?php
/**
 * @OA\Info(
 *      title="API Catégorie",
 *      version="1.0.0",
 *      description="Documentation de l'API pour la gestion des catégories"
 * )
 */

namespace App\Http\Controllers;

use App\Models\Categorie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategorieController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/categories",
     *     summary="Obtenir la liste de toutes les catégories",
     *     tags={"Catégories"},
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Response(
     *         response=200,
     *         description="Opération réussie",
     *         @OA\JsonContent(
     *             @OA\Property(property="categories", type="array", @OA\Items(ref="#/components/schemas/Categorie"))
     *         )
     *     ),
     * 
     *     @OA\Response(response=401, description="Non autorisé")
     * )
     */
    public function index()
    {
        $categories = Categorie::all()->where('etat','nSup');
        return response()->json(compact('categories'), 200);
    }

    /**
     * Afficher le formulaire de création d'une nouvelle ressource.
     */
    public function create()
    {
        //
    }

    public function rules()
    {
        return [
            'nom' => 'required|regex:/^[a-z\s]+$/',
        ];
    }
    
    public function messages()
    {
        return [
            'nom.required' => 'Désolé! Veuillez renseigner le nom de la catégorie',
        ];
    }

    /**
     * @OA\Post(
     *     path="/api/categorieStore",
     *     summary="Créer une nouvelle catégorie",
     *     tags={"Catégories"},
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(@OA\Property(property="nom", type="string"),)
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Catégorie ajoutée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Non autorisé")
     * )
     */
    public function store(Request $request)
    {
        $request->validate($this->rules(), $this->messages());
        
        $existingCategory = Categorie::where('nom', $request->nom)->where('etat', 'nSup')->first();

        if ($existingCategory !== null) {
            return response()->json("Désolé, cette catégorie existe déjà", 404);
        }

        $categorie = new Categorie();
        $categorie->nom = $request->nom;
        $categorie->save();

        return response()->json("Catégorie enregistrée avec succès", 201);
    }


    /**
     * @OA\Get(
     *     path="/api/categorieShow{id}",
     *     summary="Obtenir les détails d'une catégorie spécifique",
     *     tags={"Catégories"},
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la catégorie",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Opération réussie",
     *         @OA\JsonContent(
     *             @OA\Property(property="categorie", type="array", @OA\Items(ref="#/components/schemas/Categorie"))
     *         )
     *     ),
     *     @OA\Response(response=401, description="Non autorisé"),
     *     @OA\Response(response=404, description="Catégorie non trouvée")
     * )
     */
    public function show($id)
    {
        $categorie = Categorie::find($id);
        if ($categorie) {
            return response()->json(compact('categorie'), 200);
        } else {
            return response()->json("Catégorie non trouvée", 404);
        }
    }

    /**
     * Afficher le formulaire d'édition de la ressource spécifiée.
     */
    public function edit(Categorie $categorie)
    {
        //
    }

    /**
     * @OA\patch(
     *     path="/api/categorieUpdate{id}",
     *     summary="Mettre à jour une catégorie spécifique",
     *     tags={"Catégories"},
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la catégorie",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(@OA\Property(property="nom", type="string"),)
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Catégorie mise à jour avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Non autorisé"),
     *     @OA\Response(response=404, description="Catégorie non trouvée")
     * )
     */
    public function update(Request $request, $id)
    {
        $categorie = Categorie::find($id);
        $categorie->nom = $request->nom;
        $categorie->save();

        return response()->json("Catégorie modifiée avec succès", 201);
    }

    /**
     * @OA\Delete(
     *     path="/api/categorieDestroy{id}",
     *     summary="Supprimer une catégorie spécifique",
     *     tags={"Catégories"},
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la catégorie à supprimer",
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
     *     @OA\Response(response=404, description="Catégorie non trouvée")
     * )
     */

    public function destroy($id)
    {
        $categorie = Categorie::find($id);
        if ($categorie) {
            $categorie->delete();
            return response()->json("Catégorie supprimée complétement avec succès", 200);
        } else {
            return response()->json("Catégorie non trouvée");
        }
    }
    /**
     * @OA\Delete(
     *     path="/api/categorieSupprimer{id}",
     *     summary="Supprimer une catégorie spécifique",
     *     tags={"Catégories"},
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la catégorie à supprimer",
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
     *     @OA\Response(response=404, description="Catégorie non trouvée")
     * )
     */

    public function suppressionSimple($id)
    {
        $categorie = Categorie::find($id);
        if ($categorie) {
            $categorie->etat = "sup";
            $categorie->save();
            return response()->json("Catégorie supprimée avec succès", 200);
        } else {
            return response()->json("Catégorie non trouvée");
        }
    }

    /**
     * @OA\Get(
     *     path="/api/listeCategorieSupprimer",
     *     summary="Obtenir la liste de toutes les catégories supprimer",
     *     tags={"Catégories"},
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Response(
     *         response=200,
     *         description="Opération réussie",
     *         @OA\JsonContent(
     *             @OA\Property(property="categories", type="array", @OA\Items(ref="#/components/schemas/Categorie"))
     *         )
     *     ),
     * 
     *     @OA\Response(response=401, description="Non autorisé")
     * )
     */
    public function listeCategorieSupprimer(){
        $categories = Categorie::all()->where('etat','sup');
        return response()->json(compact('categories'), 200);
    }

    public function restaurer($id)
    {
        $categorie = Categorie::find($id);
        if ($categorie) {
            $categorie->etat = "nSup";
            $categorie->save();
            return response()->json("Catégorie restaurée avec succès", 200);
        } else {
            return response()->json("Catégorie non trouvée");
        }
    }
}

