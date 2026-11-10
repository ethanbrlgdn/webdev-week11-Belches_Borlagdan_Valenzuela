<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Normal B-tree: the schema builder can create this one.
        Schema::table('events', function (Blueprint $table) {
            $table->index(['venue_id', 'starts_at'], 'events_venue_starts_btree');
        });

        // The schema builder cannot make these, so we use raw statements.
        DB::statement('CREATE INDEX IF NOT EXISTS events_details_gin ON events USING GIN (details)');
        DB::statement("CREATE INDEX IF NOT EXISTS events_dress_code_btree ON events ((details->>'dress_code'))");
    }

    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS events_dress_code_btree');
        DB::statement('DROP INDEX IF EXISTS events_details_gin');

        Schema::table('events', function (Blueprint $table) {
            $table->dropIndex('events_venue_starts_btree');
        });
    }
};