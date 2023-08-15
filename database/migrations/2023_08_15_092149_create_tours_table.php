<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tours', function (Blueprint $table) {
            $table->uuid("id")->primary();

            // TIP: incase the table's name being referenced was changed, pass it into constrained()

            // HINT: when using uuid as foreign key, use foreignUuid() instead of foreignId()
            $table->foreignUuid("travel_id")->constrained("travels");
            
            $table->string("name");
            $table->date("starting_date");
            $table->date("ending_date");
            $table->integer("price");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tours');
    }
};
