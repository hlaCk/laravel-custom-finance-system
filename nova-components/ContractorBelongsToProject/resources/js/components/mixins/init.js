import FieldProject from "./FieldProject";
import FieldEntryCategory from "./FieldEntryCategory";
import FieldContractor from "./FieldContractor";
import { FormField, HandlesValidationErrors } from "laravel-nova";

const $_debug = true

const overrideMethods = {
    methods: {
        /*
         * Set the initial, internal value for the field.
         */
        setInitialValue() {
            this.value = this.field.value || ''
        },

        /**
         * Fill the given FormData object with the field's internal value.
         */
        fill(formData) {
            console.error('init: fill: ', this.field.attribute,this.field.value,this.value)
            formData.append( this.field.attribute, this.value || '' )

            // formData.append(
            //     this.field.attribute,
            //     this.selectedResource ? this.selectedResource.value : ''
            // )

        },

        /**
         * Update the field's internal value
         */
        handleChange: function handleChange(event) {
            this.value = event && event.target ? event.target.value : event;

            if (this.field) {
                Nova.$emit(this.field.attribute + '-change', this.value);
            }
        }
    }
}

export default [
    {
        props: [ 'resourceName', 'resourceId', 'field' ],
        data: () => (
            {loading: true}
        ),
        async created() {
        },
        mounted() {
            this.setInitialValue();

            // Add a default fill method for the field
            this.field.fill = this.fill;

            this.registerChangeListener()
            this.$once( 'hook:beforeDestroy', () => {
                this.removeChangeListener()
            } )
        },
        methods: {
            registerChangeListener() {
                $_debug && this.$on( 'project-changed', v => console.warn( 'init: ','project-changed: ', v ) )
                $_debug && this.$on( 'entry_category-changed', v => console.warn( 'init: ','entry_category-changed: ', v ) )
                console.log(67,this.field.attribute)
                // Register a global event for setting the field's value
                this.$on(this.field.attribute + '-value', function (value) {
                    this.value = value;
                });
            },
            removeChangeListener() {
                Nova.$off( 'project-changed' )
                Nova.$off( 'entry_category-changed' )
                Nova.$off( this.field.attribute + '-value' )
            },
        },
        computed: {
            data: {
                get() {
                    let $data = {};
                    for( let option in Array.from( this.field.options || [] ) ) {
                        let _option = this.field.options[ option ]
                        $data[ _option.value ] = _option.label
                    }
                    return $data
                },
                set(v) {
                    this.field.options = v
                },
            },
        },
        watch: {
            // entry_category_fieldd(n, o) {
            //     let project_select_field = document.querySelector( '#project_select_field select' );
            //     if( project_select_field ) {
            //         // project_select_field.value=null
            //         project_select_field.dispatchEvent && project_select_field.dispatchEvent( new Event( 'change' ) )
            //         project_select_field.change && project_select_field.change()
            //     }
            // },

        },
    },

    FormField,
    HandlesValidationErrors,
    FieldProject,
    FieldEntryCategory,
    FieldContractor,
    overrideMethods,
]
