<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('activities')
            ->where('progress', 100)
            ->whereNull('completed_at')
            ->update(['completed_at' => now()]);
        DB::table('activities')
            ->where('progress', 100)
            ->update(['status' => 'completada']);
        DB::table('activities')
            ->where('progress', '>', 0)
            ->where('progress', '<', 100)
            ->where('status', 'pendiente')
            ->update(['status' => 'en_progreso']);

        Schema::table('activities', function (Blueprint $table) {
            $table->enum('status', ['pendiente', 'en_progreso', 'en_revision', 'completada', 'atrasada'])
                ->default('pendiente')
                ->change();
            $table->dropColumn('progress');
        });

        Schema::table('activity_comments', function (Blueprint $table) {
            $table->string('evidence_path')->nullable();
            $table->string('evidence_name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('activities')->where('status', 'en_revision')->update(['status' => 'en_progreso']);

        Schema::table('activity_comments', function (Blueprint $table) {
            $table->dropColumn(['evidence_path', 'evidence_name']);
        });

        Schema::table('activities', function (Blueprint $table) {
            $table->unsignedTinyInteger('progress')->default(0);
            $table->enum('status', ['pendiente', 'en_progreso', 'completada', 'atrasada'])
                ->default('pendiente')
                ->change();
        });
    }
};
