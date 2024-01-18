<?php

namespace App\Http\Controllers;

use App\Models\NewsLetter;
use Illuminate\Http\Request;
/**
 * @OA\Tag(
 *     name="Newsletters",
 *     description="Endpoints for managing newsletters"
 * )
 */
class NewsLetterController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/newsletters",
     *     summary="Get all newsletters",
     *     tags={"Newsletters"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/NewsLetter")
     *         )
     *     )
     * )
     */
    public function index()
    {
        $newsLetters = NewsLetter::all();
        return response()->json(compact('newsLetters'), 200);
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
     *     path="/api/newsletterStore",
     *     summary="Subscribe to newsletter",
     *     tags={"Newsletters"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email"},
     *             @OA\Property(property="email", type="string", format="email", example="example@example.com")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Subscription successful"
     *     ),
     *     @OA\Response(response=422, description="Email already subscribed"),
     *     @OA\Response(response=400, description="Invalid input")
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:news_letters,email',
        ]);
        $existingEmail = NewsLetter::where('email', $request->input('email'))->exists();
    
        if ($existingEmail) {
            return response()->json('Cet email est déjà inscrit à la newsletter', 422);
        }
    
        $newsLetter = new NewsLetter([
            'email' => $request->input('email'),
        ]);
    
        $newsLetter->save();
    
        return response()->json('Inscription à la newsletter réussie', 201);
    }
    

    /**
     * Display the specified resource.
     */
    public function show(NewsLetter $newsLetter)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(NewsLetter $newsLetter)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, NewsLetter $newsLetter)
    {
        //
    }

     /**
     * @OA\Delete(
     *     path="/api/newsletterDestroy{id}",
     *     summary="Unsubscribe from newsletter",
     *     tags={"Newsletters"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the newsletter subscription",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Unsubscription successful"
     *     ),
     *     @OA\Response(response=404, description="Subscription not found")
     * )
     */
    public function destroy($id)
    {
        $newsLetter = NewsLetter::find($id);

        if ($newsLetter) {
            $newsLetter->delete();
            return response()->json("success','Mail supprimée avec success", 200);
        } else {
            return response()->json("Mail non trouvée");
        }
    }
}
