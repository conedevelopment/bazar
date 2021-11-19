import Inventory from './Components/Inventory';
import Prices from './Components/Prices';

document.addEventListener('root:booting', ({ detail }) => {
    detail.app.component('Inventory', Inventory);
    detail.app.component('Prices', Prices);
});
