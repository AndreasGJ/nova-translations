Nova.booting((Vue, router, store) => {
    router.addRoutes([
        {
            name: 'nova-translations',
            path: '/nova-translations',
            component: require('./components/Tool'),
        },
    ])
})
