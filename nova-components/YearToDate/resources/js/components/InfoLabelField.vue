<template>
    <default-field :field="field">
        {{ label }}
        <template slot="field">
            {{ value }}
        </template>
    </default-field>
</template>

<script>
export default {
    props: {
        field: Object,
        label: String,
        value: {
            default: '-',
        },
    },
}
</script>

<style>
/* Scoped Styles */
</style>
