<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Builder;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Capsule\Manager as Capsule;

class DatasetMigration extends Migration {
	
	public function up() {
		Capsule::schema()->create('dataset_files', function (Blueprint $table) {
			$table->bigInteger('dataset_file_id')->autoIncrement();
			$table->bigInteger('submission_id');
			$table->bigInteger('user_id');
			$table->bigInteger('file_id');
			$table->string('file_name', 30);
			$table->unique(['file_id'], 'temporary_files_id');
		});
	}

	public function down() {
		Capsule::schema()->drop('dataset_files');
	}
}
