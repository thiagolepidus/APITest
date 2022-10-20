<div id="datasetFileUploadContainer">
    <modal
		v-bind="MODAL_PROPS"
		name="datasetModal"
		@closed="setFocusToRef('datasetModalButton')"
	>
		<modal-content
			close-label="common.close"
			modal-name="datasetModal"
			title="Dataset File Upload"
		>
            <pkp-form v-bind="components.datasetFileForm" @set="set" @success="$modal.hide('datasetModal')"/>
		</modal-content>
	</modal>

    <script type="text/javascript">
        pkp.registry.init('datasetFileUploadContainer', 'Page', {$state|json_encode});
    </script>

</div>