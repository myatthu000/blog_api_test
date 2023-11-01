<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->uuid('uuid');
            $table->string("phone_number")->nullable();
            $table->enum('role',['admin','editor','author','user'])->default('author');
            $table->enum('subscriber',['subscribed','unsubscribed'])->default('subscribed');
            $table->enum('freeze_action',[1,-1])->default(1);
            $table->string('avatar')->default('storage/default_avatar/avatar.png');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
//            $table->dropColumn('uuid');
//            $table->dropColumn('role');
//            $table->dropColumn('avatar');
            $this->down();
        });
    }
};
