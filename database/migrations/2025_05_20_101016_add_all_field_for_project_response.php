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
        Schema::table('projects', function (Blueprint $table) {
            $table->string('description')->after('name')->nullable();
            $table->json('links')->after('description')->nullable();
            $table->string('parent_id')->after('links')->nullable();
            $table->boolean('is_domain')->after('parent_id')->default(false);
            $table->boolean('enabled')->after('is_domain')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('description');
            $table->dropColumn('links');
            $table->dropColumn('parent_id');
            $table->dropColumn('is_domain');
            $table->dropColumn('enabled');
            $table->dropColumn('parent_id');
        });
    }
};
