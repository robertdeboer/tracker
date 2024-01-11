<?php

declare(strict_types=1);

use App\Models\Project;
use App\Models\ProjectUser;
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
            ProjectUser::TABLE,
            function (Blueprint $table) {
                $table->id(ProjectUser::ID);
                $table->foreignId(ProjectUser::USER_ID);
                $table->foreignId(ProjectUser::PROJECT_ID);
                $table->timestamps();

                $table->unique(
                    [ProjectUser::USER_ID, ProjectUser::PROJECT_ID],
                    'UNQ_PROJECT_USERS'
                );

                $table->foreign([ProjectUser::USER_ID], 'FK_PROJECT_USERS_USER')
                      ->references(User::ID)
                      ->on(User::TABLE)
                      ->restrictOnDelete();
                $table->foreign([ProjectUser::PROJECT_ID], 'FK_PROJECT_USERS_PROJECT')
                      ->references(Project::ID)
                      ->on(Project::TABLE)
                      ->restrictOnDelete();
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(ProjectUser::TABLE);
    }
};
