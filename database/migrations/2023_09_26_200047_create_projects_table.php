<?php

declare(strict_types=1);

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(
            Project::TABLE,
            function (Blueprint $table) {
                $table->id(Project::ID);
                $table->string(Project::NAME, 255)->unique('UNQ_PROJECT_NAME');
                $table->boolean(Project::IS_ACTIVE)->default(true);
                $table->foreignId(Project::CUSTOMER_ID);
                $table->foreignId(Project::PROJECT_MANAGER_ID);
                $table->text(Project::DESCRIPTION)->nullable();
                $table->timestamps();

                $table->foreign([Project::CUSTOMER_ID], 'FK_PROJECT_CUSTOMER')
                      ->references(User::ID)
                      ->on(User::TABLE)
                      ->restrictOnDelete();
                $table->foreign([Project::PROJECT_MANAGER_ID], 'FK_PROJECT_PROJECT_MANAGER')
                      ->references(User::ID)
                      ->on(User::TABLE)
                      ->restrictOnDelete();
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(Project::TABLE);
    }
};
