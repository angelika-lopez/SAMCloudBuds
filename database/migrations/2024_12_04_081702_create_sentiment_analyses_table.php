<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('sentiment_analyses', function (Blueprint $table) {
            $table->id();
            $table->text('text');
            $table->string('overall_sentiment');
            $table->decimal('positive_score', 5, 2);
            $table->decimal('neutral_score', 5, 2);
            $table->decimal('negative_score', 5, 2);
            $table->json('entity_analysis')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sentiment_analyses');
    }
};
