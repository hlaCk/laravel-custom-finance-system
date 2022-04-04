import Vuetify from "vuetify";

// const Vuetify = require( "vuetify" );

Nova.booting( (Vue, router, store) => {
    const opts = {}
    Vue.use(new Vuetify(opts))

  router.addRoutes([
    {
      name: 'year-to-date',
      path: '/year-to-date',
      component: require('./components/Tool'),
    },
  ])
})
