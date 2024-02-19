<?php
/**
 * @OA\Info(
 *      title="Annonce API",
 *      version="1.0.0",
 *      description="API Documentation pour la gestion des Annonces"
 * )
 */

namespace App\Http\Controllers;

use App\Mail\AnnonceAccepter;
use App\Mail\AnnonceMail;
use App\Mail\AnnonceRejeter;
use App\Models\Annonce;
use App\Models\Categorie;
use App\Models\Commentaire;
use App\Models\Image;
use App\Models\NewsLetter;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class AnnonceController extends Controller
{
     /**
     * @OA\Get(
     *     path="/api/annonceValides",
     *     summary="Obtenir une liste de toutes les annonces acceptées",
     *     tags={"Annonces"},
     *     @OA\Response(
     *         response=200,
     *         description="Opération réussie",
     *         @OA\JsonContent(
     *             @OA\Property(property="annonceValides", type="array", @OA\Items(ref="#/components/schemas/Annonce"))
     *         )
     *     ),
     *     @OA\Response(response=401, description="Non autorisé")
     * )
     */
    public function annonceValides()
    {
        $annonces = [];
        $categorie = Categorie::all();
        $annonceValides = Annonce::all()->where('etat', "accepter");
        foreach ($annonceValides as $annonceValide) {
            if($annonceValide->categorie->etat !== "sup"){
                $annonces[]= $annonceValide;
            }
        }
        return response()->json(compact('annonces'), 200);

    }

     /**
     * @OA\Get(
     *     path="/api/annonceInvalides",
     *     summary="Obtenir une liste de toutes les annonces refusées",
     *     tags={"Annonces"},
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Response(
     *         response=200,
     *         description="Opération réussie",
     *         @OA\JsonContent(
     *             @OA\Property(property="annonceInvalides", type="array", @OA\Items(ref="#/components/schemas/Annonce"))
     *         )
     *     ),
     *     @OA\Response(response=401, description="Non autorisé")
     * )
     */

     public function annonceInvalides()
    {
        $annonces = [];
        $categorie = Categorie::all();
        $annonceInvalides = Annonce::all()->where('etat', "refuser");
        foreach ($annonceInvalides as $annonceInvalide) {
            if($annonceInvalide->categorie->etat !== "nSup"){
                $annonces[]= $annonceInvalide;
            }
        }
        return response()->json(compact('annonces'), 200);
    }

    /**
     * @OA\Patch(
     *     path="/api/updateEtataAnnonce{id}",
     *     summary="Mettre à jour l'état d'une annonce spécifique",
     *     tags={"Annonces"},
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de l'annonce",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Opération réussie",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Non autorisé"),
     *     @OA\Response(response=404, description="Annonce non trouvée")
     * )
     */

    public function index($id)
    {
        $annonce = Annonce::find($id);
        $users = User::all();
        if(!$annonce){
            return response()->json("annonce non trouvé",404);
        }

        if ($annonce->etat === "accepter") {
            // Mail pour l'utilisateur
            foreach ($users as $userClient) {
                if ($annonce->user_id === $userClient->id) {
                    $annonce->etat = "refuser";
                    $annonce->save(); 
                    $ccAnnonce = [
                        'title' => 'Annonce refusée',
                        'body' => 'Votre annonce a été refusée.',
                    ];
                    Mail::to($userClient->email)->send(new AnnonceRejeter($ccAnnonce));
                }
            }
        } else {
            // Mail pour l'utilisateur
            foreach ($users as $userClient) {
                if ($annonce->user_id === $userClient->id) {
                    $annonce->etat = "accepter";
                    $annonce->save();
                    $ccAnnonce = [
                        'title' => 'Annonce acceptée',
                        'body' => 'Félicitations ! Votre annonce a été acceptée.',
                    ];
                    Mail::to($userClient->email)->send(new AnnonceAccepter($ccAnnonce));
                }
            }

            // Mail pour les abonnés à la newsletter
            $newsLetters = NewsLetter::all();
            foreach ($newsLetters as $newsLetter) {
                $ccAnnonce = [
                    'title' => 'Nouvelle annonce disponible',
                    'body' => 'Une nouvelle annonce est disponible. Consultez-la dès maintenant !',
                ];
                Mail::to($newsLetter->email)->send(new AnnonceMail($ccAnnonce));
            }
        }

        return response()->json('Opération réussie', 201);
    }
     /**
     * @OA\Get(
     *     path="/api/annonceUserValides",
     *     summary="Obtenir la liste de toutes les annonces acceptées pour l'utilisateur authentifié",
     *     tags={"Annonces"},
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Response(
     *         response=200,
     *         description="Opération réussie",
     *         @OA\JsonContent(
     *             @OA\Property(property="annonceUserValides", type="array", @OA\Items(ref="#/components/schemas/Annonce"))
     *         )
     *     ),
     *     @OA\Response(response=401, description="Non autorisé")
     * )
     */


    public function annonceUserValide() 
    {
        $user = Auth::user();
        $annonces = [];
        $categorie = Categorie::all();
        $categorie = Categorie::all();
        $annonceUserValides = Annonce::where('user_id', $user->id)->where('etat', "accepter")->get();
        foreach ($annonceUserValides as $annonceUserValide) {
            if($annonceUserValide->categorie->etat !== "sup"){
                $annonces[]= $annonceUserValide;
            }
        }
        return response()->json(compact('annonces'), 200); 
     
    }
    /**
     * @OA\Get(
     *     path="/api/annonceUserInvalides",
     *     summary="Obtenir la liste de toutes les annonces refusées pour l'utilisateur authentifié",
     *     tags={"Annonces"},
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Response(
     *         response=200,
     *         description="Opération réussie",
     *         @OA\JsonContent(
     *             @OA\Property(property="annonceUserInvalides", type="array", @OA\Items(ref="#/components/schemas/Annonce"))
     *         )
     *     ),
     *     @OA\Response(response=401, description="Non autorisé")
     * )
     */

    public function annonceUserInvalide() 
    {
        $user = Auth::user();
        $annonces = [];
        $categorie = Categorie::all();
        $annonceUserInvalides = Annonce::where('user_id', $user->id)->where('etat', "refuser")->get();
        foreach ($annonceUserInvalides as $annonceUserInvalide) {
            if($annonceUserInvalide->categorie->etat !== "sup"){
                $annonces[]= $annonceUserInvalide;
            }
        }
        return response()->json(compact('annonces'), 200); 
    }


    /**
     * @OA\Get(
     *     path="/api/annoncesParCategorie{id}",
     *     summary="Obtenir les annonces par catégorie",
     *     tags={"Annonces"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la catégorie",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Opération réussie",
     *         @OA\JsonContent(
     *             @OA\Property(property="annonces", type="array", @OA\Items(ref="#/components/schemas/Annonce"))
     *         )
     *     ),
     *     @OA\Response(response=200, description="Aucune annonce trouvée avec cette catégorie"),
     * )
     */

    public function annoncesParCategorie($id)
    {
        $categorie = Categorie::find($id);

        if (!$categorie) {
            return response()->json("Catégorie non trouvée", 200);
        }
        if($categorie->etat !== "sup")
        {
            $annonces = $categorie->annonces;
        }else{
            return response()->json("Aucune annonce trouvée avec cette catégorie", 200);
        }

        return response()->json(compact('annonces'), 200);
    }

    /**
     * @OA\Get(
     *     path="/api/annoncesMisesEnAvantParCategorie",
     *     summary="Obtenir les annonces mises en avant par catégorie",
     *     tags={"Annonces"},
     *     @OA\Response(
     *         response=200,
     *         description="Opération réussie",
     *         @OA\JsonContent(
     *             @OA\Property(property="annonces", type="array", @OA\Items(ref="#/components/schemas/Annonce"))
     *         )
     *     ),
     * )
     */
    public function annoncesMisesEnAvantParCategorie()
    {
        $categories = Categorie::all();
        $cats = [];

        foreach ($categories as $categorie) {
            if($categorie->etat !== "sup"){
                $cats[] = $categorie;
            }
        }

        $annoncesMisesEnAvant = [];

        foreach ($cats as $cat) {
            $annonces = $cat->annonces()->where('etat', 'accepter')->take(3)->get();

            $annoncesMisesEnAvant[$cat->nom] = $annonces;
        }

        return response()->json(compact('annoncesMisesEnAvant'), 200);
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
            'marque' => 'required',
            'couleur' => 'required',
            'image' => 'required',
            'prix' => 'required|numeric',
            'description' => 'required',
           // 'nbrePlace' => 'required|integer',
            'localisation' => 'required',
            'moteur' => 'required',
            'annee' => 'required|integer',
            'carburant' => 'required',
            'kilometrage' => 'required',
            //'transmission' => 'required',
            'categorie_id' => 'required|exists:categories,id',
        ];
    }

    public function messages()
    {
        return [
            'nom.required' => 'Désolé ! Veuillez renseigner le nom de l\'annonce',
            'marque.required' => 'Désolé ! Veuillez renseigner la marque de l\'annonce',
            'couleur.required' => 'Désolé ! Veuillez renseigner la couleur de l\'annonce',
            'image.required' => 'Désolé ! Veuillez télécharger une image pour l\'annonce',
            'prix.required' => 'Désolé ! Veuillez renseigner le prix de l\'annonce',
            'prix.numeric' => 'Désolé ! Le prix doit être un nombre',
            'description.required' => 'Désolé ! Veuillez renseigner la description de l\'annonce',
            //'nbrePlace.required' => 'Désolé ! Veuillez renseigner le nombre de places',
            'nbrePlace.integer' => 'Désolé ! Le nombre de places doit être un nombre entier',
            'localisation.required' => 'Désolé ! Veuillez renseigner la localisation de l\'annonce',
            'moteur.required' => 'Désolé ! Veuillez renseigner le type de moteur',
            'annee.required' => 'Désolé ! Veuillez renseigner l\'année de fabrication',
            'annee.integer' => 'Désolé ! L\'année de fabrication doit être un nombre entier',
            'carburant.required' => 'Désolé ! Veuillez renseigner le type de carburant',
            'kilometrage.required' => 'Désolé ! Veuillez renseigner le type de kilometrage',
           // 'transmission.required' => 'Désolé ! Veuillez renseigner le type de transmission',
            'etat.required' => 'Désolé ! Veuillez renseigner l\'état de l\'annonce',
            'categorie_id.required' => 'Désolé ! Veuillez renseigner la catégorie de l\'annonce',
            'categorie_id.exists' => 'Désolé ! La catégorie spécifiée n\'existe pas',
        ];
    }

    /**
     * @OA\Post(
     *     path="/api/annonceStore",
     *     summary="Create a new Annonce",
     *     tags={"Annonces"},
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="nom", type="string"),
     *             @OA\Property(property="marque", type="string"),
     *             @OA\Property(property="couleur", type="string"),
     *             @OA\Property(property="image", type="string"),
     *             @OA\Property(property="prix", type="integer"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="nbrePlace", type="integer"),
     *             @OA\Property(property="localisation", type="string"),
     *             @OA\Property(property="moteur", type="string"),
     *             @OA\Property(property="annee", type="integer"),
     *             @OA\Property(property="carburant", type="string"),
     *             @OA\Property(property="carosserie", type="string"),
     *             @OA\Property(property="kilomerage", type="string"),
     *             @OA\Property(property="transmission", type="string"),
     *             @OA\Property(property="climatisation", type="string"),
     *             @OA\Property(property="categorie_id", type="integer"),
     *             @OA\Property(property="images", type="array", @OA\Items(type="file")),
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Annonce added successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */

    public function store(Request $request)
    {
      //  $request->validate($this->rules(), $this->messages());
        $user = Auth::user();
        $annonce = new Annonce();
        $annonce->nom = $request->input('nom');
        $annonce->marque = $request->input('marque');
        $annonce->couleur = $request->input('couleur');
        if($request->file('image')){
            $file= $request->file('image');
            $filename= date('YmdHi').$file->getClientOriginalName();
            $file-> move(public_path('public/images'), $filename);
            $annonce['image']= $filename;
        }
        $annonce->prix = $request->input('prix');
        $annonce->description = $request->input('description');
        $annonce->nbrePlace = $request->input('nbrePlace');
        $annonce->localisation = $request->input('localisation');
        $annonce->moteur = $request->input('moteur');
        $annonce->annee = $request->input('annee');
        $annonce->carburant = $request->input('carburant');
        $annonce->carosserie = $request->input('carosserie');
        $annonce->kilometrage = $request->input('kilometrage');
        $annonce->transmission = $request->input('transmission');
        $annonce->climatisation = $request->input('climatisation');
        if($request->file('image1')){
            $file= $request->file('image1');
            $filename= date('YmdHi').$file->getClientOriginalName();
            $file-> move(public_path('images'), $filename);
            $annonce['image1']= $filename;
        }if($request->file('image2')){
            $file= $request->file('image2');
            $filename= date('YmdHi').$file->getClientOriginalName();
            $file-> move(public_path('images'), $filename);
            $annonce['image2']= $filename;
        }if($request->file('image3')){
            $file= $request->file('image3');
            $filename= date('YmdHi').$file->getClientOriginalName();
            $file-> move(public_path('images'), $filename);
            $annonce['image3']= $filename;
        }if($request->file('image4')){
            $file= $request->file('image4');
            $filename= date('YmdHi').$file->getClientOriginalName();
            $file-> move(public_path('images'), $filename);
            $annonce['image4']= $filename;
        }
        $annonce->etat = "refuser";
        $annonce->categorie_id = $request->input('categorie_id');
        $annonce->user_id = $user->id;
        $annonce->save();
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $imageFile) {
                $filename = date('YmdHi') . '_' . uniqid() . '.' . $imageFile->getClientOriginalExtension();
                $imageFile->move(public_path('images'), $filename);
                $image = new Image();      
                $image->url = $filename;
                $image->annonce_id = $annonce->id;
                $image->save();
            }
        }
        

        return response()->json("Annonce ajoutée avec succès", 201);
    }



    /**
     * @OA\Get(
     *     path="/api/annonceShow{id}",
     *     summary="Get details of a specific Annonce",
     *     tags={"Annonces"},
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the Annonce",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="annonce", type="array", @OA\Items(ref="#/components/schemas/Annonce")),
     *             @OA\Property(property="commentaires", type="array", @OA\Items(ref="#/components/schemas/Commentaire"))
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Annonce not found")
     * )
     */
    public function show($id)
    {
        
        $annonce = Annonce::find($id);
        $user = Auth::user();
        if (!$annonce) {
            return response()->json('Annonce non trouvé', 404);
        }

        if ($user->id !== $annonce->user_id) {
            return response()->json('Vous n\'avez pas l\'autorisation de voir cette annonce', 403);
        }
        $commentaires = Commentaire::where('annonce_id', $id)->get();
        return response()->json(compact('annonce','commentaires'), 200);
    }

    /**
     * @OA\Get(
     *     path="/api/annonceDetail{id}",
     *     summary="Get details of a specific Annonce",
     *     tags={"Annonces"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the Annonce",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="annonce", type="array", @OA\Items(ref="#/components/schemas/Annonce"))
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=404, description="Annonce not found")
     * )
     */

     public function detail($id)
    {
        
        $annonce = Annonce::find($id);
        // $user = Auth::user();
        if (!$annonce) {
            return response()->json('Annonce non trouvé', 404);
        }
        $commentaires = Commentaire::where('annonce_id', $id)->get();
        return response()->json(compact('annonce','commentaires'), 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Annonce $annonce)
    {
        //
    }

    /**
     * @OA\Patch(
     *     path="/api/annonceUpdate{id}",
     *     summary="Update a specific Annonce",
     *     tags={"Annonces"},
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the Annonce",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="nom", type="string"),
     *             @OA\Property(property="marque", type="string"),
     *             @OA\Property(property="couleur", type="string"),
     *             @OA\Property(property="image", type="string"),
     *             @OA\Property(property="prix", type="integer"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="nbrePlace", type="integer"),
     *             @OA\Property(property="localisation", type="string"),
     *             @OA\Property(property="moteur", type="string"),
     *             @OA\Property(property="annee", type="integer"),
     *             @OA\Property(property="carburant", type="string"),
     *             @OA\Property(property="carosserie", type="string"),
     *             @OA\Property(property="kilomerage", type="string"),
     *             @OA\Property(property="transmission", type="string"),
     *             @OA\Property(property="climatisation", type="string"),
     *             @OA\Property(property="categorie_id", type="integer"),
     *             @OA\Property(property="images", type="array", @OA\Items(type="file")),
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Annonce updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Annonce not found")
     * )
     */

     public function update(Request $request, $id)
     {
         $user = Auth::user();
         $annonce = Annonce::find($id);
     
         if (!$annonce) {
             return response()->json('Annonce non trouvée', 404);
         }
     
         if ($user->id !== $annonce->user_id) {
             return response()->json("Vous n'avez pas l'autorisation de mettre à jour cette annonce", 403);
         }
     
         // Mise à jour des champs de l'annonce
         $annonce->nom = $request->input('nom');
         $annonce->marque = $request->input('marque');
         $annonce->couleur = $request->input('couleur');

        if ($request->file('image')) {
            if ($annonce->image) {
                $oldImagePath = public_path('images/' . $annonce->image);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }
    
            $file = $request->file('image');
            $filename = date('YmdHi') . $file->getClientOriginalName();
            $file->move(public_path('public/images'), $filename);
            $annonce->image = $filename;
        }
     
         $annonce->prix = $request->input('prix');
         $annonce->description = $request->input('description');
         $annonce->nbrePlace = $request->input('nbrePlace');
         $annonce->localisation = $request->input('localisation');
         $annonce->moteur = $request->input('moteur');
         $annonce->annee = $request->input('annee');
         $annonce->carburant = $request->input('carburant');
         $annonce->carosserie = $request->input('carosserie');
         $annonce->kilometrage = $request->input('kilometrage');
         $annonce->transmission = $request->input('transmission');
         $annonce->climatisation = $request->input('climatisation');
         if($request->file('image1')){
            $file= $request->file('image1');
            $filename= date('YmdHi').$file->getClientOriginalName();
            $file-> move(public_path('images'), $filename);
            $annonce['image1']= $filename;
        }if($request->file('image2')){
            $file= $request->file('image2');
            $filename= date('YmdHi').$file->getClientOriginalName();
            $file-> move(public_path('images'), $filename);
            $annonce['image2']= $filename;
        }if($request->file('image3')){
            $file= $request->file('image3');
            $filename= date('YmdHi').$file->getClientOriginalName();
            $file-> move(public_path('images'), $filename);
            $annonce['image3']= $filename;
        }if($request->file('image4')){
            $file= $request->file('image4');
            $filename= date('YmdHi').$file->getClientOriginalName();
            $file-> move(public_path('images'), $filename);
            $annonce['image4']= $filename;
        }
         $annonce->etat = "refuser";
         $annonce->categorie_id = $request->input('categorie_id');
         $annonce->user_id = $user->id;
         $annonce->save();
     
         // Mise à jour des images associées à l'annonce
         if ($request->hasFile('images')) {
             foreach ($request->file('images') as $imageFile) {
                 $file = $imageFile;
                 $filename = date('YmdHi') . '_' . uniqid() . '.' . $imageFile->getClientOriginalExtension();
                 $file->move(public_path('public/images'), $filename);
     
                 // Supprimer l'ancienne image si elle existe
                 if ($annonce->images->isNotEmpty()) {
                     $oldImagePath = public_path('public/images/' . $annonce->images[0]->url);
                     if (file_exists($oldImagePath)) {
                         unlink($oldImagePath);
                     }
                 }
     
                 // Mettre à jour l'image associée
                 $image = new Image();
                 $image->url = $filename;
                 $image->annonce_id = $annonce->id;
                 $image->save();
             }
         }
     
         return response()->json('Annonce modifiée avec succès', 200);
     }
     
     /**
     * @OA\Delete(
     *     path="/api/annonceDestroy{id}",
     *     summary="Delete a specific Annonce",
     *     tags={"Annonces"},
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the Annonce to delete",
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
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Annonce not found")
     * )
     */
 
    public function destroy($id)
    {
        $annonce = Annonce::find($id);
        $user = Auth::user();

        if($user->id !== $annonce->user_id) {
            return response()->json('Vous n\'avez pas l\'autorisation de suuprimer cette annonce', 403);
        }elseif ($annonce) {
            if ($annonce->image) {
                $imagePath = public_path('images/' . $annonce->image);
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }
            $annonce->delete();
            return response()->json("success','Annonce supprimée avec success", 200);
        }else {
            return response()->json("Annonce non trouvée");
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/annonceDestroyAdmin{id}",
     *     summary="Delete a specific Annonce",
     *     tags={"Annonces"},
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="l'id de l'annonce à supprimer",
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
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Annonce not found")
     * )
     */

     public function destroyAdmin($id)
    {
        $annonce = Annonce::find($id);
        if ($annonce) {
            if ($annonce->image) {
                $imagePath = public_path('images/' . $annonce->image);
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }
            $annonce->delete();
            return response()->json("success','Annonce supprimée avec success", 200);
        }else {
            return response()->json("Annonce non trouvée");
        }
    }

    /**
     * @OA\Post(
     *     path="/api/whatsap{id}",
     *     summary="whatsap",
     *     tags={"Users"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la whatsap",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Opération réussie",
     *         @OA\JsonContent(
     *             @OA\Property(property="users", type="array", @OA\Items(ref="#/components/schemas/User"))
     *         )
     *     ),
     *     @OA\Response(response=200, description="Aucune annonce trouvée avec cette catégorie"),
     * )
     */

    
    public function redirigerWhatsApp($id)
    {
        try {
            // Validation de l'ID comme étant numérique
            if (!is_numeric($id)) {
                throw new Exception('L\'ID doit être numérique.');
            }

            $annonce = Annonce::findOrFail($id);
            $numeroWhatsApp = $annonce->user->telephone;
            $urlWhatsApp = "https://api.whatsapp.com/send?phone=$numeroWhatsApp";

            return $urlWhatsApp;
        } catch (ModelNotFoundException $e) {
            return redirect()->route('whatsapp'); // Utilisez le bon nom de route
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

}
