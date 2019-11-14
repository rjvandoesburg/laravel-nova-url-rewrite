<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUrlRewritesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('url_rewrite.tables.url_rewrites'), static function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('group')->default(0);
            $table->string('request_path')->index();
            $table->string('target_path');
            $table->smallInteger('redirect_type')->default(0);
            $table->text('description')->nullable();
            $table->nullableMorphs('model');
            $table->string('resource_type')->nullable()->index();
            $table->timestamps();

            $table->unique(['group', 'request_path']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(config('url_rewrite.tables.url_rewrites'));
    }
}
