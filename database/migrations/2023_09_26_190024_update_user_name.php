<?php

declare(strict_types=1);

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
        Schema::table(
            User::TABLE,
            function (Blueprint $table) {
                $table->dropColumn(['name']);
                $table->string(User::FIRST_NAME, 125);
                $table->string(User::LAST_NAME, 125);
                $table->unique([User::FIRST_NAME,User::LAST_NAME], 'UNQ_USERS_NAME');
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table(
            User::TABLE,
            function (Blueprint $table) {
                $table->dropUnique('UNQ_USERS_NAME');
                $table->dropColumn([User::FIRST_NAME, User::LAST_NAME]);
                $table->string('name', 255);

            }
        );
    }
};
