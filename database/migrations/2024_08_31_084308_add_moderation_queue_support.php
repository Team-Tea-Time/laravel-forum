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
        Schema::table('forum_categories', function (Blueprint $table) {
            $table->boolean('thread_queue_enabled')->default(false)->after('is_private');
            $table->boolean('post_queue_enabled')->default(false)->after('thread_queue_enabled');
        });

        Schema::table('forum_threads', function (Blueprint $table) {
            $table->timestamp('approved_at')->nullable()->after('updated_at');
        });

        Schema::table('forum_posts', function (Blueprint $table) {
            $table->timestamp('approved_at')->nullable()->after('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('forum_categories', function (Blueprint $table) {
            $table->dropColumn('thread_queue_enabled');
            $table->dropColumn('post_queue_enabled');
        });

        Schema::table('forum_threads', function (Blueprint $table) {
            $table->dropColumn('approved_at');
        });

        Schema::table('forum_posts', function (Blueprint $table) {
            $table->dropColumn('approved_at');
        });
    }
};
