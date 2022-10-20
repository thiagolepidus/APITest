<?php

import('lib.pkp.classes.db.SchemaDAO');
import('plugins.generic.apiTest.classes.datasetFile.DatasetFile');

class DatasetFileDAO extends SchemaDAO {

    var $schemaName = 'datasetFile';

    var $tableName = 'dataset_files';

    var $primaryKeyColumn = 'dataset_file_id';

    var $primaryTableColumns = [
		'id' => 'dataset_file_id',
		'submissionId' => 'submission_id',
		'userId' => 'user_id',
		'fileId' => 'file_id',
		'fileName' => 'file_name',
	];

    function newDataObject() {
		return new DatasetFile();
	}

	public function getMax() {
		$queryResults = new DAOResultFactory($this->retrieve('SELECT * FROM dataset_files'), $this, '_fromRow');
		return $queryResults->toIterator();
	}

	public function getBySubmissionId($submissionId) {
		$queryResults = new DAOResultFactory(
			$this->retrieve(
				'SELECT * FROM dataset_files WHERE submission_id = ?',
				[$submissionId]
			),
			$this,
			'_fromRow'
		);
		return $queryResults->toIterator();
	}
  
	public function _fromRow($primaryRow) {
		$schemaService = Services::get('schema');
		$schema = $schemaService->get($this->schemaName);

		$object = $this->newDataObject();

		foreach ($this->primaryTableColumns as $propName => $column) {
			if (isset($primaryRow[$column])) {
			$object->setData(
				$propName,
				$this->convertFromDb($primaryRow[$column], $schema->properties->{$propName}->type)
			);
			}
		}

		return $object;
	}
}