# Changelog

## Unreleased

## v0.3.0 (2020-12-21)
### Added
- Added `Inventory`, `Prices` and `Price` attribute bags
- Added dynamic cart prices – recalculates prices, taxes and discounts when changing the cart's currency
- Added `Stockable`, `Priceable` and `Inventoryable` interfaces
- Addded `.editorconfig`
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
