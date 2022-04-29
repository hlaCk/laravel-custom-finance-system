<script>
export default {
    data: () => ({
        bTableConfig: {
            first_column_key: 'id',
            last_column_key: 'actions',
        },
    }),
    methods: {

        /**
         * OPTIONAL
         * @param d
         * @return {string}
         */
        isFirstFooterCell(d) {
            return this.isLastRow(d) && this.getFieldKey(d) === this.bTableConfig.first_column_key
        },
        /**
         * OPTIONAL
         * @param d
         * @return {string}
         */
        isLastFooterCell(d) {
            return this.isLastRow(d) && this.getFieldKey(d) === this.bTableConfig.last_column_key
        },
        /**
         * OPTIONAL
         * @param d
         * @return {string}
         */
        isFirstRow(d) {
            return this.getFieldIndex(d) === this.getFirstRowIndex(d)
        },
        /**
         * OPTIONAL
         * @param d
         * @return {string}
         */
        isLastRow(d) {
            return this.getFieldIndex(d) === this.getLastRowIndex(d)
        },

        /**
         * OPTIONAL
         * @param d
         * @return {string}
         */
        getFieldKey(d) {
            return d.field.key
        },
        /**
         * OPTIONAL
         * @param d
         * @return {string}
         */
        getFieldIndex(d) {
            return d.index
        },
        /**
         * OPTIONAL
         * @param d
         * @return {string}
         */
        getTableRowsData(d = null) {
            return this.data
        },
        /**
         * OPTIONAL
         * @param d
         * @return {string}
         */
        getLastRowIndex(d = null) {
            return Object.keys(this.getTableRowsData()).length - 1
        },
        /**
         * OPTIONAL
         * @param d
         * @return {string}
         */
        getFirstRowIndex(d = null) {
            return 0
        },

        /**
         * OPTIONAL
         * @param d
         * @return {string}
         */
        parseNormalCell(d) {
            return `${d.value}`
        },
    },
}
</script>
