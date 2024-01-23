<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
/**
 * @OA\Tag(
 *     name="Messages",
 *     description="Endpoints pour la gestion des messages"
 * )
 */
class MessageController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/messages",
     *     summary="Obtenir tous les messages",
     *     tags={"Messages"},
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Response(
     *         response=200,
     *         description="Opération réussie",
     *         @OA\JsonContent(
     *       @OA\Property(property="messages", type="array", @OA\Items(ref="chemin/vers/votre/fichier.yaml#/components/schemas/Message"))
     *      )
     *     ),
     *     @OA\Response(response=401, description="Non autorisé")
     * )
     */
    public function index()
    {
        $messages = Message::all();
        return response()->json(compact('messages'), 200);
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
     *     path="/api/messageStore",
     *     summary="Ajouter un nouveau message",
     *     tags={"Messages"},
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Ceci est un message"),
     *             @OA\Property(property="email", type="string", example="exemple@gmail.com"),
     *             @OA\Property(property="nomComplet", type="string", example="Adama Gueye")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Message ajouté avec succès",
     *     ),
     *     @OA\Response(response=401, description="Non autorisé")
     * )*/
    public function store(Request $request)
    {
        // $request->validate([
        //     'message' => 'required|string',
        //     'nomComplet' => 'required|string',
        //     'email' => 'required|string',
        // ]);


        $message = new Message([
            'message' => $request->input('message'),
            'email' => $request->input('email'),
            'nomComplet' => $request->input('nomComplet'),
        ]);

        $message->save();

        return response()->json('Message enregistré avec succès, nous vous enverrons un email pour la réponse', 201);
    }

    /**
     * @OA\Get(
     *     path="/api/messageShow{id}",
     *     summary="Obtenir un message spécifique par ID",
     *     tags={"Messages"},
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID du message",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Opération réussie",
     *         @OA\JsonContent(
     *             @OA\Property(property="messages", type="array", @OA\Items(ref="#/components/schemas/Message"))
     *          )
     *      ),
     *     @OA\Response(response=401, description="Non autorisé"),
     *     @OA\Response(response=404, description="Message non trouvé")
     * )*/
    public function show($id)
    {
        $message = Message::find($id);
        return response()->json(compact('message'), 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Message $message)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Message $message)
    {
        //
    }

    /**
     * @OA\Delete(
     *     path="/api/messageDestroy{id}",
     *     summary="Supprimer un message",
     *     tags={"Messages"},
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID du message",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Message supprimé avec succès",
     *     ),
     *     @OA\Response(response=401, description="Non autorisé"),
     *     @OA\Response(response=404, description="Message non trouvé")
     * )*/
    public function destroy($id)
    {
        $message = Message::find($id);

        if ($message) {
            $message->delete();
            return response()->json("success','Message supprimé avec succès", 200);
        } else {
            return response()->json("Message non trouvé");
        }
    }
}
