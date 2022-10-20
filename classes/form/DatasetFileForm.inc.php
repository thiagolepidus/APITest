<?php

use \PKP\components\forms\FormComponent;
use \PKP\components\forms\FieldText;
use \PKP\components\forms\FieldUpload;

class DatasetFileForm extends FormComponent {

	public function __construct($action, $locales, $temporaryFileApiUrl) {
		$this->action = $action;
		$this->locales = $locales;
		$this->id = 'datasetFileForm';
		$this->method = 'POST';

        $this->addField(new FieldUpload('datasetFile', [
			'label' => 'Dataset file',
			'isRequired' => true,
			'value' => null,
			'options' => [
				'url' => $temporaryFileApiUrl,
			],
		]));
	}
}