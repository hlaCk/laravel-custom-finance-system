import { BootstrapVue, IconsPlugin, LayoutPlugin } from 'bootstrap-vue'

Nova.booting( (Vue, router, store) => {
    Vue.use( BootstrapVue )
    Vue.use( IconsPlugin )
    Vue.use( LayoutPlugin )

    Vue.component( 'info-label-field', require( './components/InfoLabelField' ) );
    Vue.component( 'select-project-field', require( './components/SelectProjectField' ) );

    Vue.component( 'credit-summary-table', require( './components/CreditSummaryTable' ) );
    Vue.component( 'credit-by-project-table', require( './components/ShowProjectCreditByProjectInfo' ) );



    // Vue.component( 'project-info', require( './components/ShowProjectInfo' ) );
    // Vue.component( 'project-credit-info', require( './components/ShowProjectCreditInfo' ) );
    // Vue.component( 'project-expense-info', require( './components/ShowProjectExpenseInfo' ) );
    Vue.component( 'form-company-report-y-t-d', require( './components/Form' ) );
    router.addRoutes( [
                          {
                              name: 'company-report-y-t-d',
                              path: '/company-report-y-t-d',
                              component: require( './components/Tool' ),
                          },
                      ] )
} )
