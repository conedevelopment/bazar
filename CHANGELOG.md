# Changelog

## v0.8.1 (2021-05-20)
### Added
- Added the `updateItems(array $data)` and the `removeItems(array $ids)` methods to the `Cart` driver

## v0.8.0 (2021-05-20)
### Changed
- Changed the `Cart` Driver API
- Changed the `Shipping` Manager and Driver API
- Changed the `Gateway` Manager and Driver API
- Changed the `SendNewOrderNotifications` event listener to a job

### Fixed
- Fixed key-values in form components
- Fixed SVG item display in media manager

### Removed
- Removed the `Checkout` class, now it's integrated into the `Gateway` Drivers
- Removed the `Manual` Driver

## v0.7.6 (2021-05-18)
### Fixed
- Fixed non-existing media URLs

## v0.7.5 (2021-05-09)
### Added
- Added SVG support for the media library

### Changed
- Use the `Date` facade, instead of the `Carbon` class
- Passes the `Request` object for the filtering methods as a parameter

### Fixed
- Fixed data table selection issues

## v0.7.4 (2021-04-25)
### Added
- Added `env` functions for default drivers in the `bazar.php` config file

### Fixed
- Fixed shipping driver issue ([#148](https://github.com/conedevelopment/bazar/issues/148))
- Fixed `addressable` column type to `uuidMorphs` ([#157](https://github.com/conedevelopment/bazar/pull/147))
- Fixed `shippable` column type to `uuidMorphs` ([a4b150a](https://github.com/conedevelopment/bazar/commit/a4b150abde9a7bf2e55519807c49e8e6997afb4f))

## v0.7.3 (2021-04-19)
### Fixed
- Fixed proxy based route-model binding

## v0.7.2 (2021-04-19)
### Added
- Added Session Cart Driver ([#143](https://github.com/conedevelopment/bazar/pull/143))

### Fixed
- Fixed `itemable` column type to `uuidMorphs` ([#142](https://github.com/conedevelopment/bazar/pull/142))

### Changed
- Changed default fitler handling and swapped key-values for filters

## v0.7.1 (2021-04-16)
### Fixed
- Fixed `Shipping` foreign ID compatibility [#140](https://github.com/conedevelopment/bazar/issues/140)

## v0.7.0 (2021-04-13)
### Added
- Added – at least getting started – front-end (Jest) test

### Changed
- Migrated from Vue 2 to Vue 3
- Migrated from NPM to Yarn
- Changed front-end bootstrapping events

### Fixed
- Fixed persistent layout bug

## v0.6.0 (2021-03-29)
### Added
- Added retries to the uploader component, to make sure, the image is loaded after the job is finished
- Added the `HasUuid` trait
- Added the `InteractsWithProxy` trait

### Changed
- Changed the minimum PHP version from `7.3` to `7.4`
- Changed the cart ID to UUID, the `token` column is removed
- Changed `fzaninotto/faker` to `fakerphp/faker` dependency
- Changed the `uuid()` columns, made them primary key
- Changed the `options` column to `properties` on the `bazar_products` table
- Changed the `option` column to `variation` on the `bazar_variants` table

### Fixed
- Fixed Quill issues
- Fixed Media Manager item keys

### Removed
- Removed the Proxy facades

## v0.5.0 (2021-02-20)
### Added
- Added the menu repository that allows to easily register custom menu items in the admin UI

## v0.4.8 (2021-02-18)
### Added
- Added the `intertiajs/inertia-laravel` package instead of the custom implementation

### Changed
- Changed the symlinking logic to the native publishing logic that the `ServiceProvider` class offers

## v0.4.7 (2021-02-06)
### Added
- Added the new `Bazar\Conversion\Manager` class that allows to add custom image conversion drives (like intervension)

### Changed
- Changed the `Bazar\Services\Checkout` class to `Bazar\Cart\Checkout`

### Fixed
- Fixed the compression when performing conversions on PNG, that drastically reduces the file size, now it's set to `1`
- Fixed undefined variable in `customer-new-order.blade.php`
- Fixed automatic route-model binding for non-Bazar routes
- Fixed media modal z-index issue

### Removed
- Removed the `Bazar\Repositories\Conversion` class
- Removed the `Bazar\Services\Image` class
- Removed the `Bazar\Shipping\WeightBasedShipping` class

## v0.4.6 (2021-02-03)
### Added
- Added `AssetRepository` and `Asset` facade
- Added `LinkAssetsCommand` command

### Changed
- Changed product addtion when creating orders manually

## v0.4.5 (2021-02-01)
### Fixed
- Fixed Order creation error [#100](https://github.com/conedevelopment/bazar/issues/100)
- Fixed hidden Alert

## v0.4.4 (2021-01-31)
### Fixed
- Fixed Vue compiling error [#91](https://github.com/conedevelopment/bazar/issues/91)

## v0.4.3 (2021-01-30)
### Added
- Added the `--packages` option to the `bazar:publish` command, that syncs the NPM `devDependecies` in the `packages.json`
- Added the `--mix` option to the `bazar:publish` command, that appends the proper webpack tasks to compile Bazar assets

### Fixed
- Fixed the `it_can_install_bazar` test with the `--seed` option
- Fixed the `409` error when `Bazar::assetVersion()` is `null`
- Fixed the wrong usage of `App::publicPath()`

## v0.4.2 (2021-01-27)
### Fixed
- Fixed image publishing [#86](https://github.com/conedevelopment/bazar/issues/86)

## v0.4.1 (2021-01-24)
### Fixed
- Fixed checking differences between variant and selected option is too permissive [#83](https://github.com/conedevelopment/bazar/pull/83)

## v0.4.0 (2021-01-18)
### Added
- Added front-end boot events

### Fixed
- Fixed alert re-rendering issue

### Removed
- Removed moment.js integration from chart.js
- Removed and refactored some small codes

## v0.3.0 (2020-12-21)
### Added
- Added `Inventory`, `Prices` and `Price` attribute bags
- Added dynamic cart prices – recalculates prices, taxes and discounts when changing the cart's currency
- Added `Stockable`, `Priceable` and `Inventoryable` interfaces
- Added `.editorconfig`
- Added cart locking mechanism

### Fixed
- Fixed active menu classes

### Changed
- Changed `normal` key to `default` for prices
- Flattened dimensions in the inventory array/JSON
- Renamed `Variation` to `Variant`
- Proxies became Facades
- Changed chunk_expiration
- Refactored interfaces and traits

## v0.2.4 (2020-11-19)
### Added
- Added container proxies, that make container bindings easily swappable

### Changed
- Changed the cart resolution logic in the `Bazar\Cart\Driver` class

### Fixed
- Fixed columns in scopes

## v0.2.3 (2020-11-05)
### Fixed
- Fixed `softDeletes` migration on users table – skipped if already exists

## v0.2.2 (2020-11-03)
### Changed
- Bazar migration table names got the `bazar_` prefix

## v0.2.1 (2020-11-02)
### Added
- The `orders` relations has been added to the `Product` model
- The `carts` relations has been added to the `Product` model

### Changed
- The `payments` scope has been renamed to `payment` in the `Transaction` model
- The `refunds` scope has been renamed to `refund` in the `Transaction` model

## v0.2.0 (2020-10-29)
### Added
- Added `virtual` method in the `Stockable` trait
- Added `downloadable` method in the `Stockable` trait
- Added missing unit and feature tests

### Changed
- The `bazar:install` creates a symlink to the compiled assets
- `Mailabiles` are removed, using `Notifications` instead
- Reorganized migrations

### Removed
- Removed the `bazar:scaffold` command

### Fixed
- Reset item and taxable keys in the `Itemable` trait

## v0.1.0 (2020-09-09)
Initial release
