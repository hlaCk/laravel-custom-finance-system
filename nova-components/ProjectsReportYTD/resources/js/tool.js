import { BootstrapVue, IconsPlugin, LayoutPlugin } from 'bootstrap-vue'

Nova.booting( (Vue, router, store) => {
    Vue.use( BootstrapVue )
    Vue.use( IconsPlugin )
    Vue.use( LayoutPlugin )

    Vue.component( 'info-label-field', require( './components/InfoLabelField' ) );
    // Vue.component( 'select-project-field', require( './components/SelectProjectField' ) );

    Vue.component( 'project-info', require( './components/ShowProjectInfo' ) );
    Vue.component( 'project-credit-info', require( './components/ShowProjectCreditInfo' ) );
    Vue.component( 'project-expense-info', require( './components/ShowProjectExpenseInfo' ) );
    Vue.component( 'form-projects-report-y-t-d', require( './components/Form' ) );
    router.addRoutes( [
                          {
                              name: 'projects-report-y-t-d',
                              path: '/projects-report-y-t-d',
                              component: require( './components/Tool' ),
                          },
                      ] )
} )
