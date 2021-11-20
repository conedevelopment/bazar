import Inventory from './Components/Inventory';
import Prices from './Components/Prices';
import Properties from './Components/Properties';

document.addEventListener('root:booting', ({ detail }) => {
    detail.app.component('Inventory', Inventory);
    detail.app.component('Prices', Prices);
    detail.app.component('Properties', Properties);
});
