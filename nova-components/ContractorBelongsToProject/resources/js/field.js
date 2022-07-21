Nova.booting((Vue, router, store) => {
  Vue.component('index-contractor-belongs-to-project', require('./components/IndexField'));
  Vue.component('detail-contractor-belongs-to-project', require('./components/DetailField'));
  Vue.component('form-contractor-belongs-to-project', require('./components/FormField'));

});
