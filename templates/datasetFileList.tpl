<div id="datasetFileListContainer">
    <list-panel :items="components.datasetFileList.items">
        <pkp-header slot="header">
            <h2>Datasets</h2>
            <template slot="actions">
                <pkp-button ref="datasetModalButton" @click="$modal.show('datasetModal')">
                    Add file
                </pkp-button>
            </template>
        </pkp-header>
    </list-panel>
</div>
<script type="text/javascript">
    pkp.registry.init('datasetFileListContainer', 'Page', {$state|json_encode});
</script>