<template>
    <default-field :field="field">
        <!--        <b>{{ label }}</b>-->
        {{ label }}
        <template slot="field" class="font-bold">
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
