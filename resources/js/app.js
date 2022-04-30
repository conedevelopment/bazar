document.addEventListener('root:booting', ({ detail }) => {
    detail.app.component('Properties', require('./Components/Properties'));
});
