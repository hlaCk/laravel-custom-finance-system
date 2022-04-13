<template>
    <loading-view :loading="loading">
        <div>
            <code>query: {{ query }}</code>
            <datatable v-bind="$data" />
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

<script>

export default {
    provide() {
        return {
            selectedProjectId: this.selectedProjectId,
        };
    },
    data: () => (
        {
            fixHeaderAndSetBodyMaxHeight: 200,
            tblStyle: 'table-layout: fixed', // must
            tblClass: 'table-bordered',
            columns: [
                { title: 'User ID', field: 'uid', sortable: true, fixed: true },
                { title: 'Username', field: 'name' },
                { title: 'Age', field: 'age', sortable: true },
                { title: 'Email', field: 'email' },
                { title: 'Country', field: 'country', fixed: 'right' }
            ].map(col => (col.colStyle = { width: '200px' }, col)),
            data: [],
            summary: {},
            total: 0,
            query: {}
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
    watch: {
        query: {
            handler (query) {
                // mockData(query).then(({ rows, total }) => {
                //     this.data = rows
                //     this.total = total
                // })
            },
            deep: true
        }
    },
}
</script>

<style>
/* Scoped Styles */
</style>
