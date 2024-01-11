<?php

declare(strict_types=1);

use App\Models\TimeEntry;
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
            TimeEntry::TABLE,
            function (Blueprint $table) {
                $table->id(TimeEntry::ID);
                $table->foreignId(TimeEntry::WORK_ITEM_ID);
                $table->foreignId(TimeEntry::AUTHOR_ID);
                $table->float(TimeEntry::HOURS);
                $table->dateTime(TimeEntry::DATE)->default(DB::raw('NOW()'));
                $table->text(TimeEntry::NOTE)->nullable();
                $table->timestamps();

                $table->foreign([TimeEntry::WORK_ITEM_ID], 'FK_TIME_ENTRY_WORK_ITEM')
                      ->on(WorkItem::TABLE)
                      ->references(WorkItem::ID)
                      ->restrictOnDelete();
                $table->foreign([TimeEntry::AUTHOR_ID], 'FK_TIME_ENTRY_AUTHOR')
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
        Schema::dropIfExists(TimeEntry::TABLE);
    }
};
