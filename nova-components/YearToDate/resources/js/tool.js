import Datatable from 'vue2-datatable-component'

Nova.booting( (Vue, router, store) => {
    Vue.use(Datatable) // done!
    Vue.component('project-info-label-field', require('./components/InfoLabelField'));
    Vue.component('project-info', require('./components/ShowProjectInfo'));
    Vue.component('select-project-field', require('./components/SelectProjectField'));
    Vue.component('form-year-to-date', require('./components/Form'));
    router.addRoutes( [
                          {
                              name: 'year-to-date',
                              path: '/year-to-date',
                              component: require( './components/Tool' ),
                          },
                      ] )
} )
