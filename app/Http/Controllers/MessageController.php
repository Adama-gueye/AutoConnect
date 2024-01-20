<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
/**
 * @OA\Tag(
 *     name="Messages",
 *     description="Endpoints for managing messages"
 * )
 */
class MessageController extends Controller
{
     /**
     * @OA\Get(
     *     path="/api/messages",
     *     summary="Get all messages",
     *     tags={"Messages"},
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Message")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized")
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
     *     path="/api/messages",
     *     summary="Add a new message",
     *     tags={"Messages"},
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="This is a message")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Message added successfully",
     *     ),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
        ]);
    
        $user = Auth::user();
    
        $message = new Message([
            'message' => $request->input('message'),
            'user_id' => $user->id,
        ]);
    
        $message->save();
    
        return response()->json('Message enregistré avec succès, nous vous enverrons un amil pour la réponse', 201);
    }

    /**
     * @OA\Get(
     *     path="/api/messages{id}",
     *     summary="Get a specific message by ID",
     *     tags={"Messages"},
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the message",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/Message")
     *     ),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=404, description="Message not found")
     * )
     */
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
     *     summary="Delete a message",
     *     tags={"Messages"},
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the message",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Message deleted successfully",
     *     ),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=404, description="Message not found")
     * )
     */
    public function destroy($id)
    {
        $message = Message::find($id);

        if ($message) {
            $message->delete();
            return response()->json("success','Message supprimée avec success", 200);
        } else {
            return response()->json("Message non trouvée");
        }
    }
}
