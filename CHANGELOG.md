# Changelog

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
