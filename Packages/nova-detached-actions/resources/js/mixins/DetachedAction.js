import _ from 'lodash'
import {Errors, InteractsWithResourceInformation} from 'laravel-nova'
import HandlesActions from '@nova/mixins/HandlesActions'

export default {
    mixins: [HandlesActions, InteractsWithResourceInformation],

    data: () => ({
        visibleActionsDefault: 3,
        actionsList: [],
        selectedResources: 'all',
        confirmActionModalOpened: false,
        invisibleActionsOpen: false,
    }),

    /**
     * Mount the component and retrieve its initial data.
     */
    async created() {
        this.getDetachedActions()

        this.$on('actionExecuted', () => {
            Nova.$emit('refresh-resources')
        })
    },

    methods: {
        /**
         * Get the actions available for the current resource.
         */
        getDetachedActions() {
            this.actionsList = []
// view import
//             console.log('nda-getDetachedActions',this.viaResource, this.viaResourceId, this)
            return Nova.request()
                .get(`/nova-api/${this.resourceName}/actions`, {
                    params: {
                        component: 'get-detached-actions',
                        _resourceName: this.resourceName,
                        _resourceId: this.$attrs['resource-id'],
                        viaResource2: this.viaResource,
                        viaResourceId2: this.viaResourceId,
                        viaRelationship: this.viaRelationship,
                        relationshipType: this.relationshipType
                    }
                })
                .then(response => {
                    this.handleResponse(response)
                })
        },

        /**
         * Execute the selected action.
         */
        executeAction() {
            this.working = true
// view import
//             console.warn('nda-executeAction',this.selectedAction,this)
            Nova.request({
                method: 'post',
                url: this.endpoint || `/nova-api/${this.resourceName}/action`,
                params: this.actionRequestQueryString,
                data: this.actionFormData(),
            })
                .then(response => {
                    this.confirmActionModalOpened = false
                    this.handleActionResponse(response.data)
                    this.working = false
                })
                .catch(error => {
                    this.working = false

                    if (error.response.status == 422) {
                        this.errors = new Errors(error.response.data.errors)
                        Nova.error(this.__('There was a problem executing the action.'))
                    }
                })
        },

        /**
         * Gather the action FormData for the given action.
         * on index import
         */
        actionFormData() {
            return _.tap(new FormData(), formData => {
                formData.append('resources', this.selectedResources)

                _.each(this.selectedAction.fields, field => {
                    field.fill(formData)
                })
            })
        },

        /**
         * Determine whether the action should redirect or open a confirmation modal
         */
        determineActionStrategy(action) {

            this.selectedActionKey = action.uriKey;

            if (this.selectedAction.withoutConfirmation) {
                this.executeAction()
            } else {
                this.openConfirmationModal()
            }
        },

        /**
         * Handle a click on an action.
         *
         * @param {Object} action
         */
        handleClick(action) {
            return this.determineActionStrategy(action);
        },
    },

    computed: {
        /**
         * Get all of the detached actions.
         */
        detachedActions() {
            return _.filter(this.allActions, a => a.detachedAction || false)
        },

        /**
         * Get the visible detached actions.
         */
        visibleActions() {
            return this.visibleActionsLimit == 0
                ? []
                : this.detachedActions.slice(0, this.visibleActionsLimit)
        },

        /**
         * Get the invisible detached actions.
         */
        invisibleActions() {
            return this.detachedActions.slice(this.visibleActionsLimit)
        },

        /**
         * Get the visible actions limit.
         */
        visibleActionsLimit() {
            return this.resourceInformation.hasOwnProperty('visibleActionsLimit')
                ? this.resourceInformation.visibleActionsLimit
                : this.visibleActionsDefault;
        },

        /**
         * Get all of the available actions.
         */
        allActions() {
            return this.actionsList
        },

        /**
         * Show the arrow to the right of the dropdown trigger.
         */
        showInvisibleActionsArrow() {
            return this.resourceInformation.hasOwnProperty('showInvisibleActionsArrow')
                ? this.resourceInformation.showInvisibleActionsArrow
                : false
        },

        /**
         * Specify the icon to use on the dropdown trigger.
         */
        invisibleActionsIcon() {
            return this.resourceInformation.hasOwnProperty('invisibleActionsIcon')
                ? this.resourceInformation.invisibleActionsIcon
                : 'hero-more-horiz'
        },

        /**
         * Determine whether to show the dropdown trigger.
         */
        shouldShowInvisibleActions() {
            return this.detachedActions.length > this.visibleActionsLimit
        }
    }
}
