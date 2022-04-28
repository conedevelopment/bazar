document.addEventListener('root:booting', ({ detail }) => {
    detail.app.component('Inventory', require('./Components/Inventory'));
    detail.app.component('Prices', require('./Components/Prices'));
    detail.app.component('Properties', require('./Components/Properties'));
});
