<?php
/**
 * @OA\Info(
 *      title="Annonce API",
 *      version="1.0.0",
 *      description="API Documentation for managing Annonces"
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
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class AnnonceController extends Controller
{
     /**
     * @OA\Get(
     *     path="/api/annoncesValides",
     *     summary="Get a list of all accepted Annonces",
     *     tags={"Annonces"},
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="annonceValides", type="array", @OA\Items(ref="#/components/schemas/Annonce"))
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function annonceValides()
    {
        $annonceValides = Annonce::all()->where('etat', "accepter");
        return response()->json(compact('annonceValides'), 200);
    }

     /**
     * @OA\Get(
     *     path="/api/annonceInvalides",
     *     summary="Get a list of all rejected Annonces",
     *     tags={"Annonces"},
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="annonceInvalides", type="array", @OA\Items(ref="#/components/schemas/Annonce"))
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */

     public function annonceInvalides()
    {
        $annonceInvalides = Annonce::all()->where('etat', "refuser");
        return response()->json(compact('annonceInvalides'), 200);
    }

        /**
     * @OA\Patch(
     *     path="/api/index{id}",
     *     summary="Update the state of a specific Annonce",
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
     *         response=201,
     *         description="Operation successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=404, description="Annonce not found")
     * )
     */
    public function index($id)
    {
        $annonce = Annonce::find($id);
        $users = User::all();

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
     *     summary="Get a list of all accepted Annonces for the authenticated user",
     *     tags={"Annonces"},
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="annonceUserValides", type="array", @OA\Items(ref="#/components/schemas/Annonce"))
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */

    public function annonceUserValide() 
    {
        $user = Auth::user();
        $annonceUserValides = Annonce::where('user_id', $user->id)->where('etat', "accepter")->get();
        return response()->json(compact('annonceUserValides'), 200); 
     
    }
    /**
     * @OA\Get(
     *     path="/api/annonceUserInvalides",
     *     summary="Get a list of all rejected Annonces for the authenticated user",
     *     tags={"Annonces"},
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="annonceUserInvalides", type="array", @OA\Items(ref="#/components/schemas/Annonce"))
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function annonceUserInvalide() 
    {
        $user = Auth::user();
        $annonceUserInvalides = Annonce::where('user_id', $user->id)->where('etat', "refuser")->get();
        return response()->json(compact('annonceUserInvalides'), 200);  
    }

    public function redirigerWhatsApp($id)
    {
        try {
            // Validation de l'ID comme étant numérique
            if (!is_numeric($id)) {
                throw new Exception('L\'ID doit être numérique.');
            }

            $user = User::findOrFail($id);
            $numeroWhatsApp = $user->telephone;
            $urlWhatsApp = "https://api.whatsapp.com/send?phone=$numeroWhatsApp";

            return "sucess";
        } catch (ModelNotFoundException $e) {
            return redirect()->route('whatsapp.user'); // Utilisez le bon nom de route
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    /**
     * @OA\Get(
     *     path="/api/annonces{id}",
     *     summary="Get a list of Annonces for a specific Categorie",
     *     tags={"Annonces"},
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Parameter(
     *         name="categorieId",
     *         in="path",
     *         required=true,
     *         description="ID of the Categorie",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="annonces", type="array", @OA\Items(ref="#/components/schemas/Annonce"))
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=404, description="Categorie not found")
     * )
     */
    public function annoncesParCategorie($id)
    {
        $annonces = Categorie::findOrFail($id)->annonces;
            return response()->json(compact('annonces'), 200);
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
            'nbrePlace' => 'required|integer',
            'localisation' => 'required',
            'moteur' => 'required',
            'annee' => 'required|integer',
            'carburant' => 'required',
            'carosserie' => 'required',
            'transmission' => 'required',
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
            'nbrePlace.required' => 'Désolé ! Veuillez renseigner le nombre de places',
            'nbrePlace.integer' => 'Désolé ! Le nombre de places doit être un nombre entier',
            'localisation.required' => 'Désolé ! Veuillez renseigner la localisation de l\'annonce',
            'moteur.required' => 'Désolé ! Veuillez renseigner le type de moteur',
            'annee.required' => 'Désolé ! Veuillez renseigner l\'année de fabrication',
            'annee.integer' => 'Désolé ! L\'année de fabrication doit être un nombre entier',
            'carburant.required' => 'Désolé ! Veuillez renseigner le type de carburant',
            'carosserie.required' => 'Désolé ! Veuillez renseigner le type de carrosserie',
            'transmission.required' => 'Désolé ! Veuillez renseigner le type de transmission',
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
     *         @OA\JsonContent(ref="#/components/schemas/AnnonceRequest")
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
        $request->validate($this->rules(), $this->messages());
        $user = Auth::user();
        $annonce = new Annonce();
        $annonce->nom = $request->input('nom');
        $annonce->marque = $request->input('marque');
        $annonce->couleur = $request->input('couleur');
        $annonce->image = $request->input('image');
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
        $annonce->transmission = $request->input('transmission');
        $annonce->etat = "refuser";
        $annonce->categorie_id = $request->input('categorie_id');
        $annonce->user_id = $user->id;
        $annonce->save();
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $imageFile) {
                $filename = date('YmdHi') . '_' . uniqid() . '.' . $imageFile->getClientOriginalExtension();
                $imageFile->move(public_path('public/images'), $filename);
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
     *     path="/api/annonces/Detail{id}",
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
        $user = Auth::user();
        if (!$annonce) {
            return response()->json('Annonce non trouvé', 404);
        }
        return response()->json(compact('annonce'), 200);
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
     *         @OA\JsonContent(ref="#/components/schemas/AnnonceRequest")
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
    public function update(Request $request,$id)
    {
       // $request->validate($this->rules(), $this->messages());
        $user = Auth::user();
        $annonce = Annonce::find($id);
        if (!$annonce) {
            return response()->json('Annonce non trouvé', 404);
        }

        if ($user->id !== $annonce->user_id) {
            return response()->json('Vous n\'avez pas l\'autorisation de mettre à jour ce bloc', 403);
        }
        $annonce->nom = $request->input('nom');
        $annonce->marque = $request->input('marque');
        $annonce->couleur = $request->input('couleur');
        // $annonce->image = $request->input('image');
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
        $annonce->transmission = $request->input('transmission');
        $annonce->etat = "refuser";
        $annonce->categorie_id = $request->input('categorie_id');
        $annonce->user_id = $user->id;
        $annonce->save();
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $imageFile) {
                $file= $imageFile;
                $filename = date('YmdHi') . '_' . uniqid() . '.' . $imageFile->getClientOriginalExtension();
                $file->move(public_path('public/images'), $filename);
                $image = new Image();      
                $image->url = $filename;
                $image->annonce_id = $annonce->id;
                $image->save();
            }
        }

        return response()->json("Annonce modifiée avec succès", 201);
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

        if ($annonce) {
            $annonce->delete();
            return response()->json("success','Annonce supprimée avec success", 200);
        } else {
            return response()->json("Annonce non trouvée");
        }
    }
}
