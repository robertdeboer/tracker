<?php

declare(strict_types=1);

use App\Models\Order;
use App\Models\Project;
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
            Order::TABLE,
            function (Blueprint $table) {
                $table->id(Order::ID);
                $table->string(Order::REFERENCE_NUMBER)
                      ->nullable()
                      ->unique('UNQ_ORDER_REF_NUMBER');
                $table->dateTime(Order::DATE)
                      ->default(DB::raw('NOW()'));
                $table->string(Order::EMAIL);
                $table->float(Order::HOURS)
                      ->default(0.00);
                $table->foreignId(Order::PROJECT_ID)
                      ->nullable();
                $table->timestamps();

                $table->foreign([Order::PROJECT_ID], 'FK_ORDERS_PROJECT')
                      ->on(Project::TABLE)
                      ->references(Project::ID)
                      ->restrictOnDelete();
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(Order::TABLE);
    }
};
