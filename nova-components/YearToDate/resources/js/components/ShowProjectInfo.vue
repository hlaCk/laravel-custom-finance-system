<template>
    <loading-view :loading="loading">
        <div class="flex justify-start items-start">
            <div class="w-full">

                <project-info-label-field
                    :field="this.getFieldFor( {name: 'cost', value: getProjectInfo('cost_label')} )"
                    :label="__( 'Cost' )"
                    :value="getProjectInfo('cost_label')"
                />

                <project-info-label-field
                    :field="this.getFieldFor( {name: 'credit_total', value: getProjectInfo('credit_total_label')} )"
                    :label="__( 'Received Amount From Client' )"
                    :value="getProjectInfo('credit_total_label')"
                />

                <project-info-label-field
                    :field="this.getFieldFor( {name: 'remaining', value: getProjectInfo('remaining_label')} )"
                    :label="__( 'Remaining With Client' )"
                    :value="getProjectInfo('remaining_label')"
                />

                <project-info-label-field
                    :field="this.getFieldFor( {name: 'credit_count', value: getProjectInfo('credit_count')} )"
                    :label="__( 'No. of Payments YTD' )"
                    :value="getProjectInfo('credit_count')"
                />

                <project-info-label-field
                    :field="this.getFieldFor( {name: 'credit_total', value: getProjectInfo('expenses_total_label')} )"
                    :label="__( 'Project Expenses' )"
                    :value="getProjectInfo('expenses_total_label')"
                />

                <project-info-label-field
                    :field="this.getFieldFor( {name: 'credit_total', value: getProjectInfo('balance_label')} )"
                    :label="__( 'Balance YTD' )"
                    :value="getProjectInfo('balance_label')"
                />

<!-- todo: add other fields of report (block 1) -->

            </div>
        </div>
    </loading-view>
</template>

<script>

export default {
    inject: [ "selectedProjectId" ],
    // props: {
    //     projectId: {
    //         type: Number,
    //         required: false,
    //         nullable: true,
    //         default: 0,
    //     },
    // },
    data: () => (
        {
            loading: false,
            data: null,
        }
    ),
    async created() {
        this.getProject()
    },
    mounted() {
        this.registerChangeListener()
        this.$once( 'hook:beforeDestroy', () => {
            this.removeChangeListener()
        } )
    },
    methods: {
        async getProject(id = 0) {
            this.data = {}
            id = id || this.selectedProjectId
            if( id ) {
                return Nova.request()
                    .get(
                        `/nova-vendor/year-to-date/projects/${id}`,
                        {params: {}},
                    )
                    .then( (res) => res.data )
                    .then( (res) => {
                        const {data} = res;
                        this.data = data
                        return data
                    } )
                    .catch( (error) => {
                        console.error( error.response.status, error )
                    } )
                    .finally( () => {
                        this.loading = false
                    } );
            } else {
                this.loading = false
            }

            return Promise.resolve({})
        },
        registerChangeListener() {
            Nova.$on( 'project-changed', (v) => {
                // console.log(v)
                // this.loading = true
                this.getProject(v.selected)
                    .then(x=>console.log( 'ShowProjectInfo.vue:project-changed', {v,x}, this.data ))
            } )
        },

        removeChangeListener() {
            Nova.$off( 'project-changed' )
        },
        getFieldFor({name = "", value = null}) {
            return {
                name: name,
                singularLabel: name,
                value: value,
                attribute: name,
                helpText: "",
                validationKey: name,
                required: false,
                stacked: false,
                showHelpText: false,
                nullable: false,
                readonly: false,
                reverse: false,
                searchable: false,
                prefixComponent: true,
                withSubtitles: false,
                textAlign: "left",
                debounce: 100,
                options: [],
            }
        },
        getProjectInfo($key, $default = '-') {
            let value = this.data && this.data[ $key ]
            return value || typeof (value) === 'number' ? value : $default
        },
    },
    computed: {
        projectCostField() {
            return this.getFieldFor( {name: 'cost', value: this.projectCost} )
        },
        projectCost() {
            return this.data && this.data.cost_label || "-"
        },
        data2: {
            get() {
                return this.data
            },
            set(v) {
                this.data = v
            },
        },
        // selectedProjectId: {
        //     get() {
        //         return this.data.id
        //     },
        //     set(value) {
        //         this.data.id = value
        //     },
        // },
    },
}
</script>

<style>
/* Scoped Styles */
</style>
