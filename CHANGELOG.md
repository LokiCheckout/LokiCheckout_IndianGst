# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.0.0] - 21 November 2026
### Added
- Optional GST Number field for billing and shipping addresses (India only)
- Show GST Number field per address country without page reload
- GSTIN validation (format, state code and check character), server- and client-side
- Setting to make the GST Number required
- Database columns on `quote_address` and `sales_order_address`
- Customer address attribute, saved in address book and copied from quote to order
- GST Number in address formats (email, order, admin, PDF and oneline)
- GST Number in checkout sidebar summary and admin order address edit form
- Drop GST Number from addresses outside India
- PHP 8.6 compatibility
