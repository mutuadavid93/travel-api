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
        // TIP: use `str("travel")->plural()` inside Tinker to determine what Laravel
        // will use as table plural name
        Schema::create('travels', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->boolean("is_public")->default(false);
            $table->string("slug")->unique();
            $table->string("name");
            $table->text("description");
            $table->unsignedInteger("number_of_days");
            // TODO: number_of_nights is a virtual i.e. computed from "number_of_nights" = number_of_days - 1;
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('travel');
    }
};
