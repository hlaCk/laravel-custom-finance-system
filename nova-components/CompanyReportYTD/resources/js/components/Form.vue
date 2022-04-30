<template>
    <loading-view :loading="loading">

        <form
            ref="form"
            autocomplete="off"
            @submit.prevent="submitSelectedProject"
        >
            <card class="my-3">
                <select-projects-field
                    id="select_project_field"
                    :is_view="!!submitted"
                    :selected-ids.sync="project_ids"
                    ref="select_project_field"
                    v-bind="{ ...$attrs }"
                    v-on="$listeners"
                    multiple
                />

            </card>

            <!-- Update Button -->
            <div class="flex items-center">
                <progress-button
                    ref="back_button"
                    v-bind="{ ...$attrs }"
                    :class="{'hidden': !submitted}"
                    :disabled="loading"
                    :processing="loading"
                    dusk="reset_selected_project"
                    @click.native="resetSelectedProject"
                >
                    {{ __( 'Back' ) }}
                </progress-button>

                <progress-button
                    ref="prog_btn"
                    v-bind="{ ...$attrs }"
                    v-on="$listeners"
                    :class="{'hidden': !!submitted}"
                    :disabled="loading || !hasProjects()"
                    :processing="loading"
                    dusk="submit-selected-project"
                    type="submit"
                >
                    {{ __( 'Show' ) }}
                </progress-button>
            </div>
        </form>


        <hr class="my-3"
            :class="{'hidden': !submitted}" >

        <card class="my-3">
            <credit-expenses-summary-table
                :project_ids="selectedProjectIds"/>
        </card>

        <hr class="my-3"
            :class="{'hidden': !submitted}" >

        <card class="my-3">
            <credit-by-project-table
                :project_ids="selectedProjectIds"/>
        </card>

        <hr class="my-3"
            :class="{'hidden': !submitted}" >

        <card class="my-3">
            <expenses-by-project-table
                :project_ids="selectedProjectIds"/>
        </card>

        <hr class="my-3"
            :class="{'hidden': !submitted}" >

        <expense-by-category-table
            :project_ids="selectedProjectIds"/>

    </loading-view>
</template>

<script>

export default {
    provide() {
        return {
            selectedProjectIds: this.selectedProjectIds,
        };
    },
    data: () => (
        {
            isBusy: false,

            loading: false,
            submitted: false,
            selectedProjectIds: [],
            project_ids: [],
        }
    ),
    metaInfo() {
        return {
            title: 'FormCompanyReportYTD',
        }
    },
    methods: {
        resetSelectedProject() {
            this.submitted = false
            // this.selectedProjectId = 0
            this.selectedProjectIds = []
        },
        submitSelectedProject() {
            this.submitted = this.hasProjects()
            this.selectedProjectIds = Array.from(this.project_ids).filter(v=>v!==null)
        },
        hasProjects() {
            return Array.from(this.project_ids).length > 0
        }
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
    },
    watch: {
        submitted(n, o) {

        },
        project_ids(n, o) {

        },
    },
}
</script>

<style lang="scss">
hr {
    margin-top: 2rem;
    margin-bottom: 2rem;
    border: 0;
    border-top: 1px solid rgba(59, 130, 246, 0.1);
}
</style>
