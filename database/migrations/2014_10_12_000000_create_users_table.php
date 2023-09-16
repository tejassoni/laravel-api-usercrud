<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name',50)->nullable();
            $table->string('email',100)->unique();
            $table->string('mobile',13)->nullable();
            $table->char('gender',6)->nullable();
            $table->date('birthdate')->nullable();
            $table->text('address')->nullable();
            $table->mediumInteger('pincode')->nullable();
            $table->string('image',100)->nullable();
            $table->tinyInteger('status')->default(0)->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
