<template>
    <loading-view :class="{
    'hidden': !project_id
}" :loading="loading">
        <b-table
            striped
            hover
            bordered
            small
            light
            :items="data || []"
            :fields="fields || []"
            head-variant="dark"
            table-variant="light"
            table-class="mb-4 bg-white w-full"
            tbody-tr-class="whitespace-no-wrap"
            thead-tr-class="whitespace-no-wrap"
            v-bind="{ ...$attrs }"
            v-on="$listeners"
        ></b-table>
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
            grand_total: [],
            data: [],
            headers: [],
            fields: [],
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
        resetData() {
            this.data = []
            this.grand_total = []
            this.headers = []
        },
        async getProject(id = 0) {
            this.loading = true
            id = id || this.selectedProjectId
            if( id ) {
                return Nova.request()
                           .get(
                               `/nova-vendor/year-to-date/projects/${id}/credits_ytd_by_month`,
                               {params: {}},
                           )
                           .then( (res) => res.data )
                           .then( (res) => {
                               const {data,grand_total} = res;
                               const {balance, credits, headers, no_of_payments} = data;

                               this.grand_total = grand_total
                               this.data = [balance, credits, no_of_payments]
                               this.headers = headers
                               this.fields = headers.map((x)=>x && x.label ? {key: typeof(x) === 'object' && x.label || "", class: typeof(x) === 'object' && x.class || ""} : false)
                                                     .filter(x=>x)

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
    },
    watch: {
        project_id(n,o) {
            this.resetData()
            this.getProject(n)
        }
    }
}
</script>

<style>
/* Scoped Styles */
.block-container {
    overflow: hidden scroll;
    position: relative;
    right: 0px;
    left: 0px;
    border: solid;
}

.block-container div.col {
    border-width: .5px;
    border-color: aliceblue;
    background-color: var(--white);
    color: var(--90);
    padding: 5px !important;
    margin: 0px !important;
    font-size: small;
}

.font-bold {
    font-weight: 800 !important;
}
.table-striped tr:nth-child(even) {
    background-color: #f6fbffad;
}
</style>
