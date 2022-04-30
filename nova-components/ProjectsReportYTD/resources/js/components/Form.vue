<template>
    <loading-view :loading="loading">

        <form
            ref="form"
            autocomplete="off"
            @submit.prevent="submitSelectedProject"
        >
            <card class="my-3">
                <select-project-field
                    id="select_project_field"
                    :is_view="!!submitted"
                    @set-changed="selectProjectChange"
                    ref="select_project_field"
                    v-bind="{ ...$attrs }"
                    v-on="$listeners"
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
                    :disabled="loading || !project_id"
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
            <project-info
                :project_id="Number(selectedProjectId)"/>
        </card>

        <hr class="my-3"
            :class="{'hidden': !submitted}" >

        <project-credit-info
            :project_id="Number(selectedProjectId)" />

        <hr class="my-3"
            :class="{'hidden': !submitted}" >

        <project-expense-info
            :project_id="Number(selectedProjectId)"/>

    </loading-view>
</template>

<script>
import SelectProjectField from "./SelectProjectField";

export default {
    components: {
        'select-project-field': SelectProjectField,
    },
    provide() {
        return {
            selectedProjectId: this.selectedProjectId,
        };
    },
    data: () => (
        {
            isBusy: false,

            loading: false,
            submitted: false,
            selectedProjectId: 0,
            project_id: 0,
        }
    ),
    metaInfo() {
        return {
            title: 'FormProjectsReportYTD',
        }
    },
    methods: {
        resetSelectedProject() {
            this.submitted = false
            this.selectedProjectId = 0
        },
        submitSelectedProject() {
            this.submitted = !!this.project_id
            this.selectedProjectId = this.project_id
        },
        selectProjectChange({selected, projects}) {
            this.project_id = selected || 0
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
    },
    watch: {
        selectedProjectId(n, o) {

        },
        submitted(n, o) {

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
