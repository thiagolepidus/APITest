<?php

import('lib.pkp.classes.plugins.GenericPlugin');
import('plugins.generic.apiTest.classes.handler.DatasetFileUploadHandler');


class APITestPlugin extends GenericPlugin {

	public function register($category, $path, $mainContextId = NULL) {

    $success = parent::register($category, $path);

		if ($success && $this->getEnabled()) {
			HookRegistry::register('LoadComponentHandler', array($this, 'setDatasetFileUploadHandler'));
			HookRegistry::register('Dispatcher::dispatch', array($this, 'setupDatasetFileHandler'));
			HookRegistry::register('Schema::get::datasetFile', array($this, 'loadDatasetFileSchema'));

			$this->import('classes.datasetFile.DatasetFileDAO');
			$datasetFileDAO = new DatasetFileDAO;
			DAORegistry::registerDAO('DatasetFileDAO', $datasetFileDAO);

			$this->import('classes.TemplateRewriter');
			$templateRewriter = new TemplateRewriter($this);
        }

		return $success;
	}

	public function getDisplayName() {
		return 'API Test';
	}

	public function getDescription() {
		return 'API test plugin.';
	}

	public function setDatasetFileUploadHandler($hookname, $params) {
		$component =& $params[0];
		switch ($component) {
			case 'plugins.generic.apiTest.classes.handler.DatasetFileUploadHandler':
				return true;
				break;
		}
		return false;
	}

	public function setupDatasetFileHandler($hookname, $request) {
		$router = $request->getRouter();
		if ($router instanceof \APIRouter && str_contains($request->getRequestPath(), 'api/v1/datasetFiles')) {
			$this->import('api.v1.datasetFiles.DatasetFileHandler');
			$handler = new DatasetFileHandler($this);
			$router->setHandler($handler);
			$handler->getApp()->run();
			exit;
		}
		return false;
	}

	public function loadDatasetFileSchema($hookname, $params) {
		$schema = &$params[0];
		$datasetFileSchemaFile = BASE_SYS_DIR . '/plugins/generic/apiTest/schemas/datasetFile.json';

		if (file_exists($datasetFileSchemaFile)) {
			$schema = json_decode(file_get_contents($datasetFileSchemaFile));
			if (!$schema) {
				fatalError('Schema failed to decode. This usually means it is invalid JSON. Requested: ' . $datasetFileSchemaFile . '. Last JSON error: ' . json_last_error());
			}
		}

		return false;
	}

	function getInstallMigration() {
        $this->import('classes.migration.DatasetMigration');
        return new DatasetMigration();
    }

}
