<?php

class TemplateRewriter {

    private $plugin;

    public function __construct($plugin) {
        HookRegistry::register('submissionsubmitstep2form::display', array($this, 'addDatasetFileContainer'));

        $this->plugin = $plugin;
    }

    public function addDatasetFileContainer($hookName, $params) {
		$request = PKPApplication::get()->getRequest();
		$templateMgr = TemplateManager::getManager($request);

		$form = $params[0];
		$form->readUserVars(array('submissionId'));
		$submissionId = $form->getData('submissionId');

		$templateMgr->assign('submissionId', $submissionId);

		$templateMgr->registerFilter("output", array($this, 'datasetFileContainerFilter'));

		return false;
    }

    function datasetFileContainerFilter($output, $templateMgr) {
		if (
			preg_match('/<div[^>]+class="section formButtons form_buttons[^>]*"[^>]*>/', $output, $matches, PREG_OFFSET_CAPTURE)
			&& $templateMgr->template_resource == 'submission/form/step2.tpl'
		) {
			$datasetFileList = $this->getDatasetFileContainer('datasetFileList');
			$datasetFileForm = $this->getDatasetFileContainer('datasetFileForm');

			$offset = $matches[0][1];
            $newOutput = $templateMgr->fetch('string:' . $datasetFileForm);
			$newOutput .= substr($output, 0, $offset + strlen($match));
			
			
			$newOutput .= $templateMgr->fetch('string:' . $datasetFileList);
			$newOutput .= substr($output, $offset + strlen($match));
			$output = $newOutput;
			$templateMgr->unregisterFilter('output', array($this, 'datasetFileFormFilter'));
		}

		return $output;
	}

    function getDatasetFileContainer($op) {
        return '
            {capture assign=datasetFileFormUrl}
                {url 
                    router=$smarty.const.ROUTE_COMPONENT 
                    component="plugins.generic.apiTest.classes.handler.DatasetFileUploadHandler" 
                    op="'. $op .'"
                    submissionId=$submissionId
                    escape=false
                }
            {/capture}
            {load_url_in_div id=""|uniqid|escape url=$datasetFileFormUrl}
        ';
    }
}