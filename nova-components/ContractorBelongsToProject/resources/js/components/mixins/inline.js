export default {
    data() {
        return {
            showUpdateButton: false
        }
    },

    computed: {
        displayValue() {
            return this.field.displayUsingLabels
                ? _.find(this.field.options, { value: this.field.value }).label
                : this.field.value;
        }
    },

    methods: {
        /**
         * @deprecated
         * @return {Promise<*>}
         */
        async submited() {
            let formData = new FormData();

            formData.append(this.field.attribute, this.value);
            formData.append('_method', 'PUT');

            return Nova.request().post(`/nova-vendor/update-order/updateOrder/${this.resourceName}/${this.resourceId}`, formData)
                .then((response) => {
                    let label_message = "FAILED!";

                    if ( response.data.success ) {
                        let label = _.find(this.field.options, option => option.value == this.value).label;
                        label_message = `${this.field.name} updated to "${label}"`;
                    }

                    this.$toasted.show(label_message, { type: response.data.success ? 'success' : 'error' });
                    // this.$toasted.show(`${this.field.name} updated to "${label}"`, { type: 'success' });
                }, (response) => {
                    this.$toasted.show(response, { type: 'error' });
                })
                .finally(() => {
                    this.showUpdateButton = false;
                });
        }
    }
}
