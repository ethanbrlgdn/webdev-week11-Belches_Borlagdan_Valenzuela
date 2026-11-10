<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("CREATE INDEX events_description_fulltext ON events USING GIN (to_tsvector('english', description))");
    }

    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS events_description_fulltext');
    }
};