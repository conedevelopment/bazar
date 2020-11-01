# Changelog

## Unreleased
### Changed
- `payments` scope hasa been renamed to `payment` in the `Transaction` model
- `refunds` scope hasa been renamed to `refund` in the `Transaction` model

## v0.2.0 (2020-10-29)
### Added
- Added `virtial` method in the `Stockable` trait
- Added `downloadable` method in the `Stockable` trait
- Added missing unit and feature tests

### Changed
- The `bazar:install` will create a symlink to the compiled assets
- `Mailabiles` are removed, using `Notifications` instead
- Reorganized migrations

### Removed
- Removed `bazar:scaffold` command

### Fixed
- Reset item and taxable keys in the `Itemable` trait

## v0.1.0 (2020-09-09)
Initial release
