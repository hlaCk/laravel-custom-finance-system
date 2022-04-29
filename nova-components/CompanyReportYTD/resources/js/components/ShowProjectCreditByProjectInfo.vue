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
            responsive="sm"
            :items="data || []"
            :fields="fields || []"
            head-variant="dark"
            table-variant="light"
            table-class="mb-4 bg-white w-full"
            tbody-tr-class="whitespace-no-wrap"
            thead-tr-class="whitespace-no-wrap"
            v-bind="{ ...$attrs }"
            v-on="$listeners"
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
    inject: [ "selectedProjectId" ],
    mixins: [ BTableParser ],
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
            data: [],
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

        console.warn(this.bTableConfig.first_column_key)
    },
    methods: {
        getTableCellClass(d) {
            // let paddingClass = this.isFirstFooterCell(d) ? ' p-2' : ''
            return (this.isLastRow(d) ? 'font-bold' : '')// + paddingClass
        },
        getTableCellValue(d) {
            return this.isLastRow(d) || this.isFirstFooterCell(d) || this.isLastFooterCell(d) ? this.parseNormalCell(d) : this.parseSmallCell(d)
        },
        parseSmallCell(d) {
            return `<small>${d.value}</small>`
        },


        resetData() {
            this.data = []
            this.fields = []
        },
        async getProject(id = 0) {
            this.loading = true
            id = id || this.selectedProjectId
            if( id ) {
                return Nova.request()
                           .get(
                               `/nova-vendor/company-report-y-t-d/projects/credits_ytd_by_project`,
                               {params: {}},
                           )
                           .then( (res) => res.data )
                           .then( (res) => {
                               const { data: {payload,headers} } = res;

                               this.data = payload
                               let parseField = (x) => {
                                   let isObj = typeof(x) === 'object';
                                   if( x && isObj && x.label ) {
                                       x[ 'key' ] = x.key || x.label || ""
                                       return x
                                   }
                                   return false;
                               }
                               this.fields = headers.map(parseField)
                                                     .filter(x=>x)

                               return payload
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
