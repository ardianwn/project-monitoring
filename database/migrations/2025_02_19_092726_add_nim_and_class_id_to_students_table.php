<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('nim')->nullable()->after('user_id');
            $table->foreignId('class_id')->nullable()->constrained('classes')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn('nim');
            $table->dropForeign(['class_id']);
            $table->dropColumn('class_id');
        });
    }
};
