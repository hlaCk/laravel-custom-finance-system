<template>
    <panel-item :field="field" />
</template>

<script>
    import { FormField, HandlesValidationErrors } from 'laravel-nova';
    import init from './mixins/init';
    import InlineMixin from './mixins/inline';

    export default {
        mixins: [
            FormField,
            HandlesValidationErrors,
            InlineMixin,
            ...init,
        ],

        props: ['resource', 'resourceName', 'resourceId', 'field'],

        methods: {
            /**
             * @deprecated
             * @return {Promise<*>}
             */
            attemptUpdated() {
                if (this.field.detailTwoStepDisabled) {
                    return this.submit();
                }
            }
        }
    }
</script>
