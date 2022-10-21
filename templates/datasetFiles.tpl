<div id="datasetFilesContainer">
	<list-panel :items="components.datasetFilesList.items" style="margin-bottom: 1rem">
        <pkp-header slot="header">
            <h2>Datasets</h2>
			<spinner v-if="isLoading"></spinner>
            <template slot="actions">
                <pkp-button ref="datasetModalButton" @click="datasetFileModalOpen">
                    Add file
                </pkp-button>
            </template>
        </pkp-header>
    </list-panel>
    <modal
		v-bind="MODAL_PROPS"
		name="datasetModal"
		@closed="datasetFileModalClose"
	>
		<modal-content
			close-label="common.close"
			modal-name="datasetModal"
			title="Dataset File Upload"
		>
            <pkp-form 
				v-bind="components.datasetFileForm"
				@set="set"
				@success="formSuccess"
			>
			</pkp-form>
		</modal-content>
	</modal>

    <script type="text/javascript">
        pkp.registry.init('datasetFilesContainer', 'DatasetPage', {$state|json_encode});
    </script>

</div>