<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            if (Schema::hasColumn('categories', 'next_slug')) {
                $table->dropColumn('next_slug');
            }

            if (Schema::hasColumn('categories', 'is_final')) {
                $table->dropColumn('is_final');
            }
        });

        Schema::table('category_states', function (Blueprint $table) {
            if (Schema::hasColumn('category_states', 'sort')) {
                $table->dropColumn('sort');
            }
        });

        Schema::table('category_states', function (Blueprint $table) {
            $table->string('sort', 12)->default('beside');
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            if (! Schema::hasColumn('categories', 'next_slug')) {
                $table->string('next_slug')->nullable();
            }

            if (! Schema::hasColumn('categories', 'is_final')) {
                $table->boolean('is_final')->default(false);
            }
        });

        Schema::table('category_states', function (Blueprint $table) {
            if (Schema::hasColumn('category_states', 'sort')) {
                $table->dropColumn('sort');
            }
        });

        Schema::table('category_states', function (Blueprint $table) {
            $table->unsignedInteger('sort')->default(0);
        });
    }
};
