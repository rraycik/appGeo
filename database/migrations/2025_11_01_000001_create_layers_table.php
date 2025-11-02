<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Enable PostGIS extension
        DB::statement('CREATE EXTENSION IF NOT EXISTS postgis;');

        Schema::create('layers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->timestamps();
        });

        // Add geometry column via raw SQL to avoid dependency on spatial schema builders
        DB::statement('ALTER TABLE layers ADD COLUMN geometry geometry NULL');
        // Create spatial index using GIST
        DB::statement('CREATE INDEX IF NOT EXISTS layers_geometry_idx ON layers USING GIST (geometry)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('layers');
        DB::statement('DROP EXTENSION IF EXISTS postgis;');
    }
};
