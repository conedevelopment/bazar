<p align="center">
  <a href="https://bazar.conedevelopment.com">
    <br/>
    <img src="https://pineco.de/wp-content/uploads/bazar/bazar-logo.svg" alt="Bazar" width="100">
    <br/>
    <sub><strong>Thoughtful Laravel e-commerce</strong></sub>
    <br/>
    <br/>
  </a>
</p>

[![GitHub Actions](https://github.com/conedevelopment/bazar/workflows/tests/badge.svg)](https://github.com/conedevelopment/bazar/actions?query=workflow%3Atests)
[![Coverage Status](https://coveralls.io/repos/github/conedevelopment/bazar/badge.svg?branch=master)](https://coveralls.io/github/conedevelopment/bazar?branch=master)

**Bazar is a powerful "[headless](https://bazar.conedevelopment.com/docs/core-concepts#headless)" e-commerce system. Built on Laravel and Vue.**

Bazar provides a flexible and easily extensible system, respecting the Laravel conventions.

## 📚 Documentation

- [Installation](https://bazar.conedevelopment.com/docs/installation) - Before moving on, please checkout the Laravel documentation about its installation, requirements and configuration.
- [Admin](https://bazar.conedevelopment.com/docs/admin) - Bazar provides a simple and extendable admin UI that comes with a lots of built-in functionality. The UI is built on Bootstrap, Vue and Inertia.
- [Cart](https://bazar.conedevelopment.com/docs/cart) - Bazar comes with a cart service by default, which manages cart models and their functionality.
- [Checkout](https://bazar.conedevelopment.com/docs/checkout) - The checkout service is no more but a helper class that manages and chains the various steps like updating addresses, creating the order, calculating shipping cost, taxes and discounts.
- [Extensions](https://bazar.conedevelopment.com/docs/extensions) - _Soon..._
- [Gateway](https://bazar.conedevelopment.com/docs/gateway) - Gateways are responsible to handle the payment or the refund process of an order.
- [Discount](https://bazar.conedevelopment.com/docs/discount) - Bazar comes with a flexible discount support by default. You can easily manage discount definitions by using the `Bazar\Support\Facades\Discount` facade.
- [Media](https://bazar.conedevelopment.com/docs/media) - Bazar comes with a very simple yet flexible and powerful media manager component both on back-end and front-end.
- [Shipping](https://bazar.conedevelopment.com/docs/shipping) - Shippings are responsible to calculate the cost of a model that implements the `Bazar\Contracts\Shippable` contract.
- [Tax](https://bazar.conedevelopment.com/docs/tax) - Bazar comes with a flexible tax support by default. You can easily manage tax definitions by using the `Bazar\Support\Facades\Tax` facade.

## 🤝 Contributing

Thank you for considering contributing to Bazar! The contribution guide can be found in the [documentation](https://bazar.conedevelopment.com/docs/contribution).

## 📝 License

Bazar is open-sourced software licensed under the [MIT](LICENSE).
