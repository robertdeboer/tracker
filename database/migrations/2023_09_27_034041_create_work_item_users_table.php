<?php

declare(strict_types=1);

use App\Models\User;
use App\Models\WorkItem;
use App\Models\WorkItemUser;
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
            WorkItemUser::TABLE,
            function (Blueprint $table) {
                $table->id(WorkItemUser::ID);
                $table->foreignId(WorkItemUser::USER_ID);
                $table->foreignId(WorkItemUser::WORK_ITEM_ID);
                $table->timestamps();

                $table->unique(
                    [
                        WorkItemUser::USER_ID,
                        WorkItemUser::WORK_ITEM_ID
                    ],
                    'UNQ_WORK_ITEM_USERS'
                );

                $table->foreign(
                    [WorkItemUser::USER_ID],
                    'FK_WORK_ITEM_USERS_USER'
                )->on(User::TABLE)
                      ->references(User::ID)
                      ->restrictOnDelete();
                $table->foreign(
                    [WorkItemUser::WORK_ITEM_ID],
                    'FK_WORK_ITEM_USERS_WORK_ITEM'
                )->on(WorkItem::TABLE)
                      ->references(WorkItem::ID)
                      ->restrictOnDelete();
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(WorkItemUser::TABLE);
    }
};
