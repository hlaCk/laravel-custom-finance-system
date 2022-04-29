<template>
    <div>
        <heading class="mb-3">
            {{
                __('CompanyReportYTD')
            }}
        </heading>

        <form-company-report-y-t-d />
    </div>
</template>

<script>
export default {
    metaInfo() {
        return {
          title: 'CompanyReportYTD',
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
