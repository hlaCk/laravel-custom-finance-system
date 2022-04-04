Nova.booting((Vue, router, store) => {
    router.addRoutes([
        {
            name: 'permission-manager',
            path: '/permission-manager/:id',
            component: require('./components/Tool'),
        },
    ])
})
