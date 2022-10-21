<?php

import('classes.handler.Handler');

class DatasetFileUploadHandler extends Handler {

	public function datasetFiles($args, $request) {
		$plugin = PluginRegistry::getPlugin('generic', 'apitestplugin');
        $dispatcher = $request->getDispatcher();
        $context = $request->getContext();
        $currentUser = Application::get()->getRequest()->getUser();
        $templateMgr = TemplateManager::getManager($request);

        $params = [
            'submissionId' => $args['submissionId'],
            'userId' => $currentUser->getId()
        ];

        $temporaryFileApiUrl = $dispatcher->url($request, ROUTE_API, $context->getPath(), 'temporaryFiles');
        $datasetFileUrl = $dispatcher->url($request, ROUTE_API, $context->getPath(), 'datasetFiles', null, null, $params);

        $supportedFormLocales = $context->getSupportedFormLocales();
		$localeNames = AppLocale::getAllLocales();
		$locales = array_map(function($localeKey) use ($localeNames) {
			return ['key' => $localeKey, 'label' => $localeNames[$localeKey]];
		}, $supportedFormLocales);

        $plugin->import('classes.form.DatasetFileForm');
		$datasetFileForm = new DatasetFileForm($datasetFileUrl, $locales, $temporaryFileApiUrl);

        $datasetFileDAO = DAORegistry::getDAO('DatasetFileDAO');
        $datasetFilesIterator = $datasetFileDAO->getBySubmissionId($args['submissionId']);
        
        $props = Services::get('schema')->getFullProps('datasetFile');
        
        $datasetFiles = [];
        if ($datasetFilesIterator->valid()) {
			foreach ($datasetFilesIterator as $datasetFile) {
                $datasetFileProps = [];
                foreach ($props as $prop) {
                    if ($prop == 'fileName') {
                        $datasetFileProps['title'] = $datasetFile->getData($prop);
                    }
                    $datasetFileProps[$prop] = $datasetFile->getData($prop);
                }
				$datasetFiles[] = $datasetFileProps;
			}
		}
        ksort($datasetFiles);

        $templateMgr->assign('state', [
			'components' => [
                'datasetFilesList' => [
                    'items' => $datasetFiles
                ],
                'datasetFileForm' => $datasetFileForm->getConfig(),
            ],
		]);
        
        return $templateMgr->fetchJson($plugin->getTemplateResource('datasetFiles.tpl'));
    }

}
