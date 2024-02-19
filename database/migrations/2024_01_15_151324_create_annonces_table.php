<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('annonces', function (Blueprint $table) {
            $table->id();
            $table->string("nom");
            $table->string("marque");
            $table->string("couleur");
            $table->string("image");
            $table->integer("prix");
            $table->text("description");
            $table->string("nbrePlace")->nullable();
            $table->string("localisation");
            $table->string("moteur");
            $table->string("annee");
            $table->string("carburant");
            $table->string("kilometrage");
            $table->string("carosserie")->nullable();
            $table->string("transmission")->nullable();
            $table->enum("climatisation",['Oui','Non'])->nullable();
            $table->string("image1")->nullable();
            $table->string("image2")->nullable();
            $table->string("image3")->nullable();
            $table->string("image4")->nullable();
            $table->enum("etat",["accepter","refuser"])->default("refuser");
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade'); 
            $table->unsignedBigInteger('categorie_id');
            $table->foreign('categorie_id')->references('id')->on('categories')->onDelete('cascade')->onUpdate('cascade'); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('annonces');
    }
};
