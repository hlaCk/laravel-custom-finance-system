<template>
    <loading-view :class="{
            'hidden': !hasProjects()
        }" :loading="loading">
        <b-table
            :items="data || []"
            :fields="fields || []"
            v-bind="{ ...$attrs }"
            v-on="$listeners"
            head-variant="dark"
            table-variant="light"
            table-class="mb-4 bg-white w-full"
            tbody-tr-class="whitespace-no-wrap"
            thead-tr-class="whitespace-no-wrap"
            responsive="sm"
            striped
            hover
            bordered
            small
            light
        >
            <!-- A custom formatted column -->
            <template #cell()="data">
                <span :class="getTableCellClass(data)" v-html="getTableCellValue(data)"></span>
            </template>

        </b-table>
    </loading-view>
</template>

<script>
import BTableParser from "../mixins/BTableParser";

export default {
    inject: [ "selectedProjectIds" ],
    mixins: [ BTableParser ],
    props: {
        project_ids: {
            type: Array,
            required: true,
        },
    },
    data: () => (
        {
            loading: false,
            data: [],
            fields: [],
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
        getTableCellClass(d) {
            // let paddingClass = this.isFirstFooterCell(d) ? ' p-2' : ''
            return (this.isLastRow(d) ? 'font-bold' : '')// + paddingClass
        },
        getTableCellValue(d) {
            return this.isLastRow(d) || this.isFirstFooterCell(d) || this.isLastFooterCell(d) ?
                   this.parseNormalCell(d) :
                   this.parseSmallCell(d)
        },
        parseSmallCell(d) {
            return `<small>${d.value}</small>`
        },


        hasProjects() {
            return Array.from(this.project_ids).length > 0
        },
        resetData() {
            this.data = []
            this.headers = []
        },
        async getProject(id = 0) {
            if( !this.hasProjects() ) return Promise.resolve(false)

            this.loading = true

            // let $project_ids = Array.from(this.project_ids).join(',')
            return Nova.request()
                       .post(
                           `/nova-vendor/company-report-y-t-d/projects/expenses_ytd_by_category_month`,
                           {project_ids: this.project_ids},
                       )
                       .then( (res) => res.data )
                       .then( (res) => {
                           const {data: main_data} = res;
                           const {payload: data, headers} = main_data;

                           this.data = data
                           this.headers = headers
                           this.fields = headers.map((x) => {
                                                    let isObj = typeof(x) === 'object';
                                                    if( x && isObj && x.label ) {
                                                        x[ 'key' ] = x.key || x.label || ""
                                                        return x
                                                    } else {
                                                        return {
                                                            'label': x,
                                                            'key': x,
                                                        };
                                                    }

                                                    return false;
                                                })
                                                .filter(x=>x)

                           return data
                       } )
                       .catch( (error) => {
                           console.error( error.response.status, error )
                       } )
                       .finally( () => {
                           this.loading = false
                       } );
        },
        registerChangeListener() {
            // Nova.$on( 'project-changed', (v) => {
                // console.log(v)
                // this.loading = true
                // this.getProject(v.selected)
            // } )
        },

        removeChangeListener() {
            // Nova.$off( 'project-changed' )
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
        project_ids(n,o) {
            this.resetData()
            this.getProject()
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
