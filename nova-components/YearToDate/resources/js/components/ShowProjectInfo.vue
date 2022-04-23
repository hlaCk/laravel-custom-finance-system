<template>
    <loading-view :class="{
    'd-none': !project_id
}" :loading="loading">
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
                    :field="this.getFieldFor( {name: 'expenses_total', value: getProjectInfo('expenses_total_label')} )"
                    :label="__( 'Project Expenses' )"
                    :value="getProjectInfo('expenses_total_label')"
                />

                <project-info-label-field
                    :field="this.getFieldFor( {name: 'balance', value: getProjectInfo('balance_label')} )"
                    :label="__( 'Balance YTD' )"
                    :value="getProjectInfo('balance_label')"
                />
            </div>
        </div>
    </loading-view>
</template>

<script>

export default {
    inject: [ "selectedProjectId" ],
    props: {
        project_id: {
            type: Number,
            required: false,
            nullable: true,
            default: 0,
        },
    },
    data: () => (
        {
            loading: false,
            data: null,
        }
    ),
    async created() {
        this.getProject(this.project_id)
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
            this.loading = true
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

            return Promise.resolve( {} )
        },
        registerChangeListener() {
            Nova.$on( 'project-changed', (v) => {
                // console.log(v)
                // this.loading = true
                // this.getProject(v.selected)
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
            return value || typeof (
                value
            ) === 'number' ? value : $default
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
    },
    watch: {
        project_id(n,o) {
            this.getProject(n)
        }
    }
}
</script>

<style>
/* Scoped Styles */
</style>
