<template>
    <div>
        <heading class="mb-3">
            {{
                __('Projects Report YTD')
            }}
        </heading>

        <form-projects-report-y-t-d />
    </div>
</template>

<script>
export default {
    metaInfo() {
        return {
          title: 'ProjectsReportYTD',
        }
    },
    mounted() {
        //
    },
}
</script>

<style>
/* Scoped Styles */
</style>
