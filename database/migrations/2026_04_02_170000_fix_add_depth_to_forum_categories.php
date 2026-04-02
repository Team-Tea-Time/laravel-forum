<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use Aimeos\Nestedset\NestedSet;

use TeamTeaTime\Forum\Models\Category;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('forum_categories', 'depth')) {
            return;
        }

        Schema::table('forum_categories', function (Blueprint $table) {
            NestedSet::columnsDepth($table);
        });

        Category::fixTree();
    }

    public function down(): void
    {
        Schema::table('forum_categories', function (Blueprint $table) {
            NestedSet::dropColumnsDepth($table);
        });
    }
};
