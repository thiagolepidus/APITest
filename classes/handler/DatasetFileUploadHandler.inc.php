<?php

import('classes.handler.Handler');

class DatasetFileUploadHandler extends Handler {

	public function datasetFileList($args, $request) {
		$plugin = PluginRegistry::getPlugin('generic', 'apitestplugin');
        $templateMgr = TemplateManager::getManager($request);

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

        $templateMgr->assign('state', [
			'components' => [
                'datasetFileList' => [
                    'items' => $datasetFiles
                ]
            ]
		]);
        
        return $templateMgr->fetchJson($plugin->getTemplateResource('datasetFileList.tpl'));
    }

    public function datasetFileForm($args, $request) {
        $dispatcher = $request->getDispatcher();
        $context = $request->getContext();
		$plugin = PluginRegistry::getPlugin('generic', 'apitestplugin');
        $templateMgr = TemplateManager::getManager($request);
        $currentUser = Application::get()->getRequest()->getUser();

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

        $templateMgr->assign('state', [
			'components' => [
				'datasetFileForm' => 	$datasetFileForm->getConfig(),
			],
		]);
        
        return $templateMgr->fetchJson($plugin->getTemplateResource('datasetFileForm.tpl'));
    }

}
