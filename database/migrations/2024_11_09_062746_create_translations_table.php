<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('translations', function (Blueprint $table) {
            $table->id();
            $table->morphs('translatable'); // This creates translatable_id and translatable_type columns
            $table->string('locale');       // e.g., 'en', 'es', 'fr'
            $table->string('attribute');    // Attribute name like 'name', 'description'
            $table->text('value')->nullable();         // Translated text
            $table->timestamps();

            $table->unique(['translatable_id', 'translatable_type', 'locale', 'attribute'], 'unique_translation');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('translations');
    }
}
