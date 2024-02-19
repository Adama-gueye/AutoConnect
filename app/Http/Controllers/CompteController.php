<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *      title="Compte API",
 *      version="1.0.0",
 *      description="API Documentation pour la gestion des comptes utilisateurs"
 * )
 */

class CompteController extends Controller
{

    /**
     * @OA\Get(
     *     path="/api/listeProprietaire",
     *     summary="Obtenir une liste de tous les utilisateurs avec le rôle 'proprietaire'",
     *     tags={"Users"},
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Response(
     *         response=200,
     *         description="Opération réussie",
     *         @OA\JsonContent(
     *             @OA\Property(property="proprietaire", type="array", @OA\Items(ref="#/components/schemas/User"))
     *         )
     *     ),
     *     @OA\Response(response=401, description="Non autorisé")
     * )
     */
    public function ListeProprietaire() {
        $proprietaire = User::where('role', 'proprietaire')->get();
        return response()->json(compact('proprietaire'), 200);
    }

    /**
     * @OA\Get(
     *     path="/api/listeAcheteur",
     *     summary="Obtenir une liste de tous les utilisateurs avec le rôle 'acheteur'",
     *     tags={"Users"},
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Response(
     *         response=200,
     *         description="Opération réussie",
     *         @OA\JsonContent(
     *             @OA\Property(property="acheteur", type="array", @OA\Items(ref="#/components/schemas/User"))
     *         )
     *     ),
     *     @OA\Response(response=401, description="Non autorisé")
     * )
     */
    public function ListeAcheteur() {
        $acheteur = User::where('role', 'acheteur')->get();
        return response()->json(compact('acheteur'), 200);
    }

     /**
     * @OA\Get(
     *     path="/api/listeUtilisateur",
     *     summary="Obtenir une liste de tous les utilisateurs",
     *     tags={"Users"},
     *     @OA\Response(
     *         response=200,
     *         description="Opération réussie",
     *         @OA\JsonContent(
     *             @OA\Property(property="user", type="array", @OA\Items(ref="#/components/schemas/User"))
     *         )
     *     ),
     *     @OA\Response(response=401, description="Non autorisé")
     * )
     */

     public function users() {
        $users = User::all();
        return response()->json(compact('users'), 200);
    }


    /**
     * @OA\Post(
     *     path="/api/register",
     *     summary="Enregistrer un nouveau compte utilisateur",
     *     tags={"Compte"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="nom", type="string", example="John"),
     *             @OA\Property(property="prenom", type="string", example="Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password"),
     *             @OA\Property(property="confirmation", type="string", format="password", example="password"),
     *             @OA\Property(property="telephone", type="string", example="123456789"),
     *             @OA\Property(property="description", type="string", example="bjhscbjhcdbjhb  eie ehebjhe efbhebjhej"),
     *             @OA\Property(property="adresse", type="string", example="Guédiawaye"),
     *             @OA\Property(property="role", type="string", example="acheteur")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Compte utilisateur créé avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", description="Compte créé avec succès"),
     *             @OA\Property(property="user", type="object", ref="#/components/schemas/User"),
     *         )
     *     ),
     *     @OA\Response(response=401, description="Erreur de validation")
     * )
     */
   public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'required|regex:/^[a-zA-Z\s]+$/',
            'prenom' => 'required|regex:/^[a-zA-Z\s]+$/',
            'telephone' => ['required', 'string', 'regex:/^(77|76|75|78|33)[0-9]{7}$/','unique:users'],
            'adresse' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
            'confirmation' => 'required|string|same:password',
        ]);
        
        
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()], 422);
        }        

        $user = new User();
        $user->nom = $request->nom;
        $user->prenom = $request->prenom;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->telephone = $request->telephone;
        $user->description = $request->description;
        $user->adresse = $request->adresse;
        if($request->file('image')){
            $file= $request->file('image');
            $filename= date('YmdHi').$file->getClientOriginalName();
            $file-> move(public_path('images'), $filename);
            $user['image']= $filename;
        }
        $user->role = $request->role;
        if($request->password!==$request->confirmation){
            return response()->json('mot de passe non identique');
        }else{
            $user->save();
            return response()->json(['message' => 'Compte créé avec succès', 'user' => $user], 201);
        }
        
    }

    /**
     * @OA\Patch(
     *     path="/api/acheteurUpdate{id}",
     *     summary="Mettre à jour un compte utilisateur existant",
     *     tags={"Compte"},
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *  @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de l'utilisateur",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="nom", type="string", example="John"),
     *             @OA\Property(property="prenom", type="string", example="Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="newpassword"),
     *             @OA\Property(property="confirmation", type="string", format="password", example="newpassword"),
     *             @OA\Property(property="telephone", type="string", example="123456789"),
     *             @OA\Property(property="description", type="string", example="bjhscbjhcdbjhb eie ehebjhe efbhebjhej"),
     *             @OA\Property(property="adresse", type="string", example="Guédiawaye"),
     *             @OA\Property(property="role", type="string", example="acheteur")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Compte utilisateur modifié avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", description="Compte modifié avec succès"),
     *             @OA\Property(property="user", type="object", ref="#/components/schemas/User"),
     *         )
     *     ),
     *     @OA\Response(response=401, description="Erreur de validation")
     * )
     */
    public function update(Request $request, $id)
    {
        

        $utilisateur = User::find($id);
        $user = Auth::user();


        if (!$utilisateur) {
            return response()->json('Utilisateur non trouvé', 404);
        }elseif($user->id !== $utilisateur->id){
            return response()->json('impossible de modifier');
        }
        $validator = Validator::make($request->all(), [
            'nom' => 'required|regex:/^[a-zA-Z\s]+$/',
            'prenom' => 'required|regex:/^[a-zA-Z\s]+$/',
            'telephone' => ['required', 'string', 'regex:/^(77|76|75|78|33)[0-9]{7}$/','unique:users'],
            'adresse' => 'required|string',
            'email' => 'required|email|unique:users',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()], 422);
        } 
       
            $utilisateur->nom = $request->nom;
            $utilisateur->prenom = $request->prenom;
            $utilisateur->email = $request->email;
            $utilisateur->telephone = $request->telephone;
            $utilisateur->description = $request->description;
            $utilisateur->adresse = $request->adresse;
            if($request->file('image')){
                $file= $request->file('image');
                $filename= date('YmdHi').$file->getClientOriginalName();
                $file-> move(public_path('images'), $filename);
                $utilisateur['image']= $filename;
            }
            $utilisateur->role = $user->role;
            $utilisateur->save();
            return response()->json(['message' => 'Votre profil a été avec succès', 'user' => $utilisateur], 201);
    }
   


    /**
     * @OA\Get(
     *     path="/api/acheteurShow{id}",
     *     summary="Obtenir les détails d'un utilisateur spécifique",
     *     tags={"Compte"},
     *     security={
     *        {"bearerAuth": {}}
     *     },
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de l'utilisateur",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Opération réussie",
     *         @OA\JsonContent(
     *             @OA\Property(property="users", ref="#/components/schemas/User")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Utilisateur non trouvé"),
     *     @OA\Response(response=401, description="Non autorisé")
     * )
     */
    public function show($id)
    {
        $utilisateur = User::find($id);
        $user = Auth::user();
        if (!$utilisateur) {
            return response()->json('Utilisateur non trouvé', 404);
        }elseif($user->id !== $utilisateur->id){
            return response()->json('Impossible de voir les infos de cette utilisateur', 404);
        }else{
            return response()->json(compact('utilisateur'), 200);
        }

    }

    /**
     * @OA\Delete(
     *     path="/api/userDestroy{id}",
     *     summary="Supprimer un utilisateur spécifique",
     *     tags={"Compte"},
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de l'utilisateur",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Utilisateur supprimé avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Utilisateur non trouvé"),
     *     @OA\Response(response=401, description="Non autorisé")
     * )
     */
    public function destroy($id)
    {
        $utilisateur = User::find($id);

        if (!$utilisateur) {
            return response()->json('Utilisateur non trouvé', 404);
        }

        $utilisateur->delete();

        return response()->json('Utilisateur supprimé avec succès', 200);
    }

}
