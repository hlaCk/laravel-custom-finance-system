<template>
    <div>
        <heading class="mb-3">
            {{
                __('Projects Report YTD')
            }}
        </heading>

        <form-year-to-date />
    </div>
</template>

<script>
export default {
    metaInfo() {
        return {
          title: 'YearToDate',
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
