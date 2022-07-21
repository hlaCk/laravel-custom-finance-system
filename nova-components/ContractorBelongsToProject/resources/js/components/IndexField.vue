<template>
    <span>{{ field.value }}</span>
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
        props: ['resourceName', 'field'],
        // data: () => ({
        //     'resourceName':null
        // }),
        // mounted() {
        //     this.resourceName = this.field.resourceUriKey || this.$router.currentRoute.params.resourceName;
        // },
        computed: {
            resourceId() {
                return this.$parent.resource.id.value;
            }
        },

        methods: {
            /**
             * @deprecated
             */
            attemptUpdated() {
                this.submit();//.then(x=>this.getResources());
                setTimeout(() => this.reloadResources(), 500);
            },

            /**
             * @deprecated
             * @return {Promise<void>}
             */
            async reloadResourcesd(){
                if(this.resourceName){
                    let filters_backup = _.cloneDeep(this.$store.getters[`${this.resourceName}/filters`]);
                    let filters_to_change = _.cloneDeep(filters_backup);
                    filters_to_change.push({});
                    await this.$store.commit(`${this.resourceName}/storeFilters`,filters_to_change);
                }
            }
        }
    }
</script>


