// import Datatable from 'vue2-datatable-component'
import {BootstrapVue, IconsPlugin} from 'bootstrap-vue'
// import { BootstrapVue } from 'bootstrap-vue'
// Import Bootstrap and BootstrapVue CSS files (order is important)
// import 'bootstrap/dist/css/bootstrap.css'
// import 'bootstrap-vue/dist/bootstrap-vue.css'


Nova.booting( (Vue, router, store) => {
    Vue.use(BootstrapVue)
    Vue.use(IconsPlugin)

    // Vue.use(Datatable) // done!
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
