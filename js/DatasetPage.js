var DatasetPage = $.extend(true, {}, pkp.controllers.Page, {
    // Here you add your custom Vue component data, methods, etc
    data() {
        return {
            notifications: [],
            latestGetRequest: '',
            activeForm: null,
            isLoading: false
        }
    },
    methods: {
        refreshItems() {
            var self = this;

            this.isLoading = true;

            this.latestGetRequest = $.pkp.classes.Helper.uuid();

            $.ajax({
				url: this.components.datasetFileForm.action,
				type: 'GET',
				_uuid: this.latestGetRequest,
				error: function(r) {
					if (self.latestGetRequest !== this._uuid) {
						return;
					}
					self.ajaxErrorCallback(r);
				},
				success: function(r) {
					if (self.latestGetRequest !== this._uuid) {
						return;
					}
					self.setItems(r.items, r.items.length);
				},
				complete() {
					if (self.latestGetRequest !== this._uuid) {
						return;
					}
					self.isLoading = false;
				}
			});
        },
        setItems(items) {
            items.map(item => item.title = item.fileName);
			this.components.datasetFilesList.items = items;
		},
        datasetFileModalClose() {
            this.setFocusToRef('datasetModalButton');
        },
        datasetFileModalOpen() {          
            this.components.datasetFileForm.fields.map(f => f.value = '');
            this.$modal.show('datasetModal');
        },
        formSuccess(data) {
            this.refreshItems();
            this.$modal.hide('datasetModal');
        }
    }
});

pkp.controllers['DatasetPage'] = DatasetPage;