<?php

import('lib.pkp.classes.handler.APIHandler');

class DatasetFileHandler extends APIHandler {

	public $schemaName = 'datasetFile';

    public function __construct() {
		$this->_handlerPath = 'datasetFiles';
        $this->_endpoints = array(
            'GET' => array(
                array(
                    'pattern' => $this->getEndpointPattern(),
                    'handler' => array($this, 'getMany'),
                ),
            ),
            'POST' => array(
                array(
                    'pattern' => $this->getEndpointPattern(),
					'handler' => array($this, 'add'),
                )
            )
        );
        parent::__construct();
    }

    public function getMany($slimRequest, $response, $args) {
        $requestParams = $slimRequest->getQueryParams();

        $datasetFileDAO = DAORegistry::getDAO('DatasetFileDAO');

        $submissionId = null;
        foreach ($requestParams as $param => $value) {
            if ($param == 'submissionId')
                $submissionId = $value;
        }

        $result = is_null($submissionId) ? $datasetFileDAO->getMax() : $datasetFileDAO->getBySubmissionId($submissionId);
        
        $items = [];
        if ($result->valid()) {
			foreach ($result as $datasetFile) {
				$items[] = $this->getFullProperties($datasetFile);
			}
		}

        ksort($items);

        return $response->withJson([
			'items' => $items,
		], 200);
    }

    public function add($slimRequest, $response, $args) {
        $queryParams = $slimRequest->getQueryParams();
        
        if (empty($queryParams['submissionId'])) {
            return $response->withStatus(404)->withJsonError('Id da submissão não informado na requisão.');
        }

        if (empty($queryParams['userId'])) {
            return $response->withStatus(404)->withJsonError('Id da usuário não informado na requisão.');
        }

        $requestParams = $slimRequest->getParsedBody();
        $fileId = $requestParams['datasetFile']['temporaryFileId'];

        import('lib.pkp.classes.file.TemporaryFileManager');
        $temporaryFileManager = new TemporaryFileManager();
		$file = $temporaryFileManager->getFile($fileId, $queryParams['userId']);
        
        $params['submissionId'] = $queryParams['submissionId'];
        $params['userId'] = $file->getUserId();
        $params['fileId'] = $file->getId();
        $params['fileName'] = $file->getOriginalFileName();
        $params = $this->convertStringsToSchema($this->schemaName, $params);

        $datasetFileDAO = DAORegistry::getDAO('DatasetFileDAO');
        $datasetFile = $datasetFileDAO->newDataObject();
        $datasetFile->setAllData($params);
        $datasetFile->setId($datasetFileDAO->insertObject($datasetFile));

        $datasetFileProps = $this->getFullProperties($datasetFile);

        return $response->withJson($datasetFileProps, 200);
    }

    private function getFullProperties($object) {
        $props = Services::get('schema')->getFullProps($this->schemaName);

        $objectProps = [];
        foreach ($props as $prop) {
            $objectProps[$prop] = $object->getData($prop);
        }

        return $objectProps;
    }
}