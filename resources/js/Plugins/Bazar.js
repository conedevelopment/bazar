import Dashboard from './../Pages/Dashboard';
import Support from './../Pages/Support';
import Profile from './../Pages/Profile';
import Password from './../Pages/Password';
// import AddressesIndex from './../Pages/Addresses/Index';
// import AddressesShow from './../Pages/Addresses/Show';
// import AddressesCreate from './../Pages/Addresses/Create';
// import CategoriesIndex from './../Pages/Categories/Index';
// import CategoriesShow from './../Pages/Categories/Show';
// import CategoriesCreate from './../Pages/Categories/Create';
import OrdersIndex from './../Pages/Orders/Index';
import OrdersShow from './../Pages/Orders/Show';
import OrdersCreate from './../Pages/Orders/Create';
import ProductsIndex from './../Pages/Products/Index';
import ProductsShow from './../Pages/Products/Show';
import ProductsCreate from './../Pages/Products/Create';
import UsersIndex from './../Pages/Users/Index';
import UsersShow from './../Pages/Users/Show';
import UsersCreate from './../Pages/Users/Create';
// import VariantsIndex from './../Pages/Variants/Index';
// import VariantsShow from './../Pages/Variants/Show';
// import VariantsCreate from './../Pages/Variants/Create';

export default {
    install(app) {
        Object.assign(window.Bazar.pages, {
            'Dashboard': Dashboard,
            'Support': Support,
            'Profile': Profile,
            'Password': Password,
            // 'Addresses/Index': AddressesIndex,
            // 'Addresses/Show': AddressesShow,
            // 'Addresses/Create': AddressesCreate,
            // 'Categories/Index': CategoriesIndex,
            // 'Categories/Show': CategoriesShow,
            // 'Categories/Create': CategoriesCreate,
            'Orders/Index': OrdersIndex,
            'Orders/Show': OrdersShow,
            'Orders/Create': OrdersCreate,
            'Products/Index': ProductsIndex,
            'Products/Show': ProductsShow,
            'Products/Create': ProductsCreate,
            'Users/Index': UsersIndex,
            'Users/Show': UsersShow,
            'Users/Create': UsersCreate,
            // 'Variants/Index': VariantsIndex,
            // 'Variants/Show': VariantsShow,
            // 'Variants/Create': VariantsCreate,
        });
    },
}
