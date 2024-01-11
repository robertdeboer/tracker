<?php

declare(strict_types=1);

use App\Models\Project;
use App\Models\User;
use App\Models\WorkItem;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(
            WorkItem::TABLE,
            function (Blueprint $table) {
                $table->id(WorkItem::ID);
                $table->foreignId(WorkItem::PROJECT_ID);
                $table->foreignId(WorkItem::OWNER_ID);
                $table->string(WorkItem::NAME, 255);
                $table->boolean(WorkItem::IS_OPEN)
                      ->default(1);
                $table->dateTime(WorkItem::START_DATE)
                      ->default(DB::raw('NOW()'));
                $table->dateTime(WorkItem::END_DATE)->nullable();
                $table->json(WorkItem::TICKET_DATA);
                $table->timestamps();

                $table->unique(
                    [WorkItem::PROJECT_ID, WorkItem::NAME],
                    'UNQ_WORK_ITEM'
                );

                $table->foreign([WorkItem::PROJECT_ID], 'FK_WORK_ITEMS_PROJECT')
                      ->on(Project::TABLE)
                      ->references(Project::ID)
                      ->restrictOnDelete();
                $table->foreign([WorkItem::OWNER_ID], 'FK_WORK_ITEMS_OWNER')
                      ->on(User::TABLE)
                      ->references(User::ID)
                      ->restrictOnDelete();
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_items');
    }
};
