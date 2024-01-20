<?php
/**
 * @OA\Info(
 *      title="Categorie API",
 *      version="1.0.0",
 *      description="API Documentation for managing Categories"
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
     *     summary="Get a list of all Categories",
     *     tags={"Categories"},
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="categories", type="array", @OA\Items(ref="#/components/schemas/Categorie"))
     *         )
     *     ),
     * 
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function index()
    {
        $categories = Categorie::all();
        return response()->json(compact('categories'), 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    public function rules()
    {
        return [
            'nom' => 'required',
        ];
    }
    public function messages()
    {
        return [
            'nom.required' => 'Desolé! veuilez renseigner le nom de la catégorie',
        ];
    }
    /**
     * @OA\Post(
     *     path="/api/categorieStore",
     *     summary="Create a new Categorie",
     *     tags={"Categories"},
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/CategorieRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Categorie added successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function store(Request $request)
    {
        $request->validate($this->rules(), $this->messages());
        $categorie = new Categorie();
        $categorie->nom = $request->nom;
        $categorie->save();

        return response()->json("Catégorie enregistrer avec succes", 201);
    }

    /**
     * @OA\Get(
     *     path="/api/categorie{id}",
     *     summary="Get details of a specific Categorie",
     *     tags={"Categories"},
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the Categorie",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="categorie", type="array", @OA\Items(ref="#/components/schemas/Categorie"))
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=404, description="Categorie not found")
     * )
     */
    public function show($id)
    {
        $categorie = Categorie::find($id);
        if ($categorie) {
            return response()->json(compact('categorie'), 200);
        } else {
            return response()->json("Categorie non trouvée", 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Categorie $categorie)
    {
        //
    }

    /**
     * @OA\patch(
     *     path="/api/categorieUpdate{id}",
     *     summary="Update a specific Categorie",
     *     tags={"Categories"},
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the Categorie",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/CategorieRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Categorie updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=404, description="Categorie not found")
     * )
     */
    public function update(Request $request, $id)
    {
       // $request->validate($this->rules(), $this->messages());
        $categorie = Categorie::find($id);
        $categorie->nom = $request->nom;
        $categorie->save();

        return response()->json("Catégorie modifier avec succes", 201);
    }

    /**
     * @OA\Delete(
     *     path="/api/categorieDestroy{id}",
     *     summary="Delete a specific Categorie",
     *     tags={"Categories"},
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the Categorie to delete",
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
     *     @OA\Response(response=404, description="Categorie not found")
     * )
     */
    public function destroy($id)
    {
        $categorie = Categorie::find($id);
        if ($categorie) {
            $categorie->delete();
            return response()->json("success','Categorie supprimée avec success", 200);
        } else {
            return response()->json("Categorie non trouvée");
        }
    }
}
