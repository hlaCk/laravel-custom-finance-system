<template>
    <loading-view :loading="loading">
        <b-container class="bv-example-row">
            <b-row>
                <b-col>1 of 2</b-col>
                <b-col>2 of 2</b-col>
            </b-row>

            <b-row>
                <b-col>1 of 3</b-col>
                <b-col>2 of 3</b-col>
                <b-col>3 of 3</b-col>
            </b-row>
        </b-container>
        <div>
            <b-table
                id="my-table"
                tableClass="w-full"
                table-variant="light"
                        :items="myProvider"
                :fields="fields"
                striped
                small
                bordered
                hover
                responsive
                show-empty
            >
                <template #table-caption>This is a table caption.</template>
                <template #empty="scope">
                    <h4>{{ __('emptyText') }}</h4>
                </template>
                <template #emptyfiltered="scope">
                    <h4>{{ __('emptyFilteredText') }}</h4>
                </template>
            </b-table>
        </div>
        <div>Selected: {{ selectedProjectId || '0' }}: {{ selectedProject || '-' }}</div>
        <form
            ref="form"
            autocomplete="off"
            @submit.prevent="submitSelectedProject"
        >
            <card class="my-3">
                <select-project-field
                    @set-changed="selectProjectChange"/>
            </card>

            <card class="my-3">
                <project-info/>

            </card>

            <!-- Update Button -->
            <div class="flex items-center">
                <!--                <cancel-button @click="$router.back()" />-->

                <progress-button
                    :disabled="loading"
                    :processing="loading"
                    dusk="submit-selected-project"
                    type="submit"
                >
                    {{ __( 'Submit' ) }}
                </progress-button>
            </div>
        </form>

    </loading-view>
</template>

<!--<script src="//unpkg.com/jquery@2.1.4/dist/jquery.min.js"></script>-->
<!--<script src="//unpkg.com/bootstrap@3.3.5/dist/js/bootstrap.min.js"></script>-->

<script>

// import 'bootstrap';

// Import Bootstrap and BootstrapVue CSS files (order is important)
// require('vue2-datatable-component')
// require('vue2-datatable-component/dist/min.css')
// require('vue2-datatable-component/dist/min.js')
// import 'bootstrap-vue/dist/bootstrap-vue.css'

export default {
    provide() {
        return {
            selectedProjectId: this.selectedProjectId,
        };
    },
    data: () => ({
            isBusy: false,
            // Note 'isActive' is left out and will not appear in the rendered table
            fields: [
                {
                    key: 'id',
                    sortable: true
                },
                {
                    key: 'name',
                    sortable: false
                },
                {
                    key: 'cost',
                    label: 'COST',
                    sortable: true,
                    // Variant applies to the whole column, including the header and footer
                    variant: 'danger'
                }
            ],
            items: [
                { isActive: true, age: 40, first_name: 'Dickerson', last_name: 'Macdonald' },
                { isActive: false, age: 21, first_name: 'Larsen', last_name: 'Shaw' },
                { isActive: false, age: 89, first_name: 'Geneva', last_name: 'Wilson' },
                { isActive: true, age: 38, first_name: 'Jami', last_name: 'Carney' }
            ],

            // totalRows: 1,
            // currentPage: 1,
            // perPage: 1,
            // pageOptions: [5, 10, 15, { value: 100, text: "Show a lot" }],

            loading: false,
            selectedProject: null,
            selectedProjectId: 0,
        }
    ),
    metaInfo() {
        return {
            title: 'FormYearToDate',
        }
    },
    async created() {
    },
    mounted() {
    },
    methods: {
        myProvider (ctx, callback) {
            this.isBusy = true
            // Here we don't set isBusy prop, so busy state will be
            // handled by table itself
            // this.isBusy = true
            let promise = axios.get(`/nova-vendor/year-to-date/some/url?page=${ctx.currentPage}&size=${ctx.perPage}`)

            promise.then((data) => {
                const $data = data.data
                // Here we could override the busy state, setting isBusy to false
                this.isBusy = false
                // this.totalRows = $data.total
                // this.currentPage = $data.current_page
                this.items = $data.data

                // Provide the array of items to the callback
                callback(this.items)
            }).catch(error => {
                // Here we could override the busy state, setting isBusy to false
                this.isBusy = false
                // Returning an empty array, allows table to correctly handle
                // internal busy state in case of error
                callback(this.items=[])
            })

            // Must return null or undefined to signal b-table that callback is being used
            return null
        },
        submitSelectedProject() {
            this.loading = true
            axios
                .post( `/nova-vendor/year-to-date/projects/${this.selectedProjectId}`, {} )
                .then( (res) => res.data )
                .then( (res) => {
                    console.log( res )
                    //Perform Success Action
                } )
                .catch( (error) => {
                    console.error( error )
                    // error.response.status Check status code
                } )
                .finally( () => {
                    this.loading = false
                } );
        },
        selectProjectChange({selected, projects}) {
            this.selectedProject = null
            this.selectedProjectId = selected
            this.selectedProject = projects[ selected ]
            // console.log(this.selectedProjectId,this.selectedProject, {projects, selected})
            return;
            // this.loading = true

            axios
                .get( `/nova-vendor/year-to-date/projects/${this.selectedProjectId}`, {} )
                .then( (res) => res.data )
                .then( (res) => {
                    const {data} = res;
                    this.selectedProject = data
                    // todo: show project info (first block)
                    console.log( data )
                } )
                .catch( (error) => {
                    console.error( error.response.status, error )
                    // error.response.status Check status code
                } )
                .finally( () => {
                    this.loading = false
                } );
        },
    },
    computed: {
        rows() {
            return this.items.length
        },
        projects: {
            get() {
                let $projects = {};
                for( let option in Array.from( this.project.options ) ) {
                    let _option = this.project.options[ option ]
                    $projects[ _option.value ] = _option.label
                }
                return $projects
            },
            set(v) {
                this.project.options = v
            },
        },
        // selectedProjectId() {
        //     return this.selectedProject && this.selectedProject.value
        // }
    },
}
</script>

<style lang="scss" >

//@import "bootstrap/dist/css/bootstrap";
//@import "~bootstrap/scss/bootstrap-reboot";
//@import "~bootstrap-vue/src/index";

//@import "~bootstrap/scss/bootstrap-grid";
//@import "~bootstrap/scss/tables";
/*div[name='HeaderSettings'] button {*/
/*    height: auto;*/
/*}*/

/*.w-240 {*/
/*    width: 240px;*/
/*}*/
/* Scoped Styles */
</style>
