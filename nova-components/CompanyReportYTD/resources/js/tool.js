import { BootstrapVue, IconsPlugin, LayoutPlugin } from 'bootstrap-vue'

Nova.booting( (Vue, router, store) => {
    Vue.use( BootstrapVue )
    Vue.use( IconsPlugin )
    Vue.use( LayoutPlugin )

    // Vue.component( 'info-label-field', require( './components/InfoLabelField' ) );
    Vue.component( 'select-project-field', require( './components/SelectProjectField' ) );
    Vue.component( 'select-projects-field', require( './components/SelectProjectsField' ) );
    // Vue.component( 'vue-multi-select', require( './components/VueMultiSelect' ) );

    Vue.component( 'credit-expenses-summary-table', require( './components/CreditExpensesSummaryTable' ) );
    Vue.component( 'credit-by-project-table', require( './components/CreditByProjectTable' ) );
    Vue.component( 'expenses-by-project-table', require( './components/ExpensesByProjectTable' ) );

    Vue.component( 'expense-by-category-table', require( './components/ExpenseByCategoryTable' ) );
    // Vue.component( 'project-expense-info', require( './components/ShowProjectExpenseInfo' ) );


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
