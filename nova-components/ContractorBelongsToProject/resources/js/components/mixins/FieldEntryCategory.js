import config from "../../config";

let $_debug = config.debug_mode && config.field_types.entry_category

export default {
    async created() {
        $_debug && console.warn( 'FieldEntryCategory: ', 'created' )
        this.loadEntryCategories()
            .then( this.initEntryCategoryField )
    },
    mounted() {
        $_debug && console.warn( 'FieldEntryCategory: ', 'mounted' )
        this.registerEntryCategoryListener()
        this.$once( 'hook:beforeDestroy', () => {
            this.removeEntryCategoryListener()
        } )
    },
    data: () => (
        {
            entry_categories: [],
            entry_category_has_constructor: !true,
        }
    ),
    methods: {
        initEntryCategoryField() {
            let entry_categories_interval = setInterval( () => {
                if( this.entry_category_field ) {
                    if( this.getEntryCategoryId() ) {
                        entry_categories_interval && clearInterval( entry_categories_interval )
                        this.$emit( 'entry_category-changed', this.getEntryCategoryId() )
                        this.handleEntryCategoryChange(this.getEntryCategoryId())
                    }
                } else {
                    entry_categories_interval && clearInterval( entry_categories_interval )
                    this.$emit( 'entry_category-changed', undefined )
                    this.handleEntryCategoryChange(this.getEntryCategoryId())
                }
            }, 10 )

            setTimeout( () => {
                entry_categories_interval && clearInterval( entry_categories_interval )
            }, 10000 )
        },
        loadEntryCategories() {
            if( this.entry_categories.length ) {
                return Promise.resolve( this.entry_categories )
            }
            this.entry_categories = []
            this.loading = true

            return Nova.request()
                       .get( `/api/entry_category/only_has_contractors/ids`, {params: {}} )
                       .then( (res) => res.data )
                       .then( (res) => {
                           const {data} = res;
                           this.entry_categories = data
                           this.handleEntryCategoryChange(this.getEntryCategoryId())
                           return data
                       } )
                       .catch( (error) => {
                           console.error( error.response.status, error )
                       } )
                       .finally( () => {
                           this.loading = false
                       } );
        },
        getEntryCategoryId(cb = undefined) {
            let result = undefined
            if( typeof cb != 'function' ) {
                cb = (x) => x
            }

            if( this.entry_category_field ) {
                let element = document.querySelector( `[dusk='${this.entry_category_field}']` );
                result = element && element.value
            }
            return cb( result )
        },
        isEntryCategoryHasConstructors() {
            return this.entry_category_has_constructor = this.entry_category_field ?
                   this.entry_categories.includes( Number( this.getEntryCategoryId() ) ) :
                   false
        },
        handleEntryCategoryChange(v) {
            $_debug && console.warn( 'FieldEntryCategory: ', 'handleEntryCategoryChange' )
            this.entry_category_has_constructor = this.isEntryCategoryHasConstructors()

            this.$emit( 'entry_category-changed', v )
        },

        registerEntryCategoryListener() {
            $_debug && console.warn( 'FieldEntryCategory: ', 'registerEntryCategoryListener' )
            Nova.$on( 'entry_category-change', this.handleEntryCategoryChange )
        },
        removeEntryCategoryListener() {
            $_debug && console.warn( 'FieldEntryCategory: ', 'removeEntryCategoryListener' )
            Nova.$off( 'entry_category-change' )
        },
    },
    computed: {
        entry_category_field() {
            return this.field.entry_category_field
        },
    },
    watch: {
        entry_categoriesa() {
            setTimeout( () => {
                this.$emit( 'entry_category-changed', this.entry_category_has_constructor = this.isEntryCategoryHasConstructors() )

                this.handleEntryCategoryChange(this.getEntryCategoryId())

                $_debug && console.warn( 'FieldEntryCategory: ', 'watch:entry_categories', {
                    entry_category_has_constructor: this.entry_category_has_constructor,
                    isEntryCategoryHasConstructors: [this.isEntryCategoryHasConstructors,this.isEntryCategoryHasConstructors()],
                    getEntryCategoryId: [this.getEntryCategoryId,this.getEntryCategoryId()],
                    entry_category_field: this.entry_category_field,
                    entry_categories: this.entry_categories,
                    includes: this.entry_categories.includes( Number( this.getEntryCategoryId() ) )
                } )
            }, 10)
        },
    },
}
