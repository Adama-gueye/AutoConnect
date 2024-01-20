<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *      title="Compte API",
 *      version="1.0.0",
 *      description="API Documentation for managing user accounts"
 * )
 */

class CompteController extends Controller
{

        /**
     * @OA\Get(
     *     path="/api/listeProprietaire",
     *     summary="Get a list of all users with the 'proprietaire' role",
     *     tags={"Users"},
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="proprietaire", type="array", @OA\Items(ref="#/components/schemas/User"))
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function ListeProprietaire() {
        $proprietaire = User::where('role', 'proprietaire')->get();
        return response()->json(compact('proprietaire'), 200);
    }

    /**
     * @OA\Get(
     *     path="/api/listeAcheteur",
     *     summary="Get a list of all users with the 'acheteur' role",
     *     tags={"Users"},
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="acheteur", type="array", @OA\Items(ref="#/components/schemas/User"))
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function ListeAcheteur() {
        $acheteur = User::where('role', 'acheteur')->get();
        return response()->json(compact('acheteur'), 200);
    }


    /**
     * @OA\Post(
     *     path="/api/register",
     *     summary="Register a new user account",
     *     tags={"Compte"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="nom", type="string", example="John"),
     *             @OA\Property(property="prenom", type="string", example="Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password"),
     *             @OA\Property(property="telephone", type="string", example="123456789"),
     *             @OA\Property(property="description", type="string", example="bjhscbjhcdbjhb  eie ehebjhe efbhebjhej"),
     *             @OA\Property(property="adresse", type="string", example="Guédiawaye"),
     *             @OA\Property(property="role", type="string", example="acheteur")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User account created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", description="Account created successfully"),
     *             @OA\Property(property="user", type="object", ref="#/components/schemas/User"),
     *         )
     *     ),
     *     @OA\Response(response=401, description="Validation Error")
     * )
     */
   public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string',
            'prenom' => 'required|string',
            'telephone' => 'required|string',
            'adresse' => 'required|string',
            'description' => 'required|string',
           // 'image' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()], 401);
        }

        $user = new User();
        $user->nom = $request->nom;
        $user->prenom = $request->prenom;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->telephone = $request->telephone;
        $user->description = $request->description;
        $user->adresse = $request->adresse;
        $user->image = $request->image;
        $user->role = $request->role;
        $user->save();

        return response()->json(['message' => 'Account created successfully', 'user' => $user], 201);
    }
}
