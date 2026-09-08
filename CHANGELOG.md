# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- Optional `gst_number` address field for billing and shipping addresses
- GSTIN format validation (server-side `IndianGstNumberValidator` + client-side `LokiComponentValidator.validators.indianGstNumber`)
- Country-specific display via `loki_checkout_country_in.xml` (India only)
- Database column on `quote_address` and `sales_order_address`
- Customer address EAV attribute with data patch
- Fieldset entries for quote-to-order, customer-to-quote, and save-in-address-book copy
- Address format observer (`customer_address_format`) for email/order/admin display
- Sidebar preview blocks showing GST number in checkout summary
- Admin order address edit form support via EAV `used_in_forms`
