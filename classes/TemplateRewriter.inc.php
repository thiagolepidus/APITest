<?php

class TemplateRewriter {

    private $plugin;

    public function __construct($plugin) {
        HookRegistry::register('submissionsubmitstep2form::display', array($this, 'addDatasetFilesContainer'));
        HookRegistry::register('TemplateManager::display', array($this, 'addDatasetPageComponent'));

        $this->plugin = $plugin;
    }

    public function addDatasetPageComponent($hookName, $params) {
        $templateMgr = &$params[0];
        $request = PKPApplication::get()->getRequest();

        $templateMgr->addJavaScript(
            'datasetPage',
            $request->getBaseUrl() . DIRECTORY_SEPARATOR . $this->plugin->getPluginPath() . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR . 'DatasetPage.js',
            [
                'contexts' => ['backend'],
                'priority' => STYLE_SEQUENCE_LAST,
            ]
        );
    }

    public function addDatasetFilesContainer($hookName, $params) {
		$request = PKPApplication::get()->getRequest();
		$templateMgr = TemplateManager::getManager($request);

		$form = $params[0];
		$form->readUserVars(array('submissionId'));
		$submissionId = $form->getData('submissionId');

		$templateMgr->assign('submissionId', $submissionId);

		$templateMgr->registerFilter("output", array($this, 'datasetFilesContainerFilter'));

		return false;
    }

    function datasetFilesContainerFilter($output, $templateMgr) {
		if (
			preg_match('/<div[^>]+class="section formButtons form_buttons[^>]*"[^>]*>/', $output, $matches, PREG_OFFSET_CAPTURE)
			&& $templateMgr->template_resource == 'submission/form/step2.tpl'
		) {
			$datasetFilesContainer = $this->getDatasetFileContainer();
            $newOutput = $templateMgr->fetch('string:' . $datasetFilesContainer);
			$newOutput .= $output;
			$output = $newOutput;
			$templateMgr->unregisterFilter('output', array($this, 'datasetFileFormFilter'));
		}

		return $output;
	}

    function getDatasetFileContainer() {
        return '
            {capture assign=datasetFileFormUrl}
                {url 
                    router=$smarty.const.ROUTE_COMPONENT 
                    component="plugins.generic.apiTest.classes.handler.DatasetFileUploadHandler" 
                    op="datasetFiles"
                    submissionId=$submissionId
                    escape=false
                }
            {/capture}
            {load_url_in_div id=""|uniqid|escape url=$datasetFileFormUrl}
        ';
    }
}