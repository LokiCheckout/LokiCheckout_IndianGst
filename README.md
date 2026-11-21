# LokiCheckout_IndianGst

<!-- badges.specs.start -->
<div class="github-specs-badges">

![Magento version](https://img.shields.io/badge/Magento-2.4.6%20%7C%202.4.9-orange)
![PHP version](https://img.shields.io/badge/PHP-8.2%E2%80%938.5-777BB4)
![License](https://img.shields.io/badge/License-OSL--3.0-blue)
![Latest Version](https://img.shields.io/packagist/v/loki-checkout/magento2-indian-gst)

</div>
<!-- badges.specs.end -->


Adds a GST Number (GSTIN) field to billing and shipping addresses in the Loki Checkout.

## Features

- `gst_number` field on both the billing and the shipping address form
- Shown per address, only when the country of that address is India (`IN`), without page reload
- The GST Number is removed from an address when its country changes away from India
- Optional by default; can be made required in the admin
- Server-side and client-side validation of the GSTIN:
  - format (`22AAAAA0000A1ZC`)
  - state code (`01` to `38`, `96`, `97` or `99`)
  - check character (15th character)
- Lowercase letters and spaces are accepted and stored as uppercase without spaces
- Copied to `sales_order_address` on order placement (only for addresses in India)
- Saved to the customer address EAV attribute when "save in address book" is checked
- Prefilled from a saved customer address on return visits
- Displayed as `GST: <number>` in the checkout summary, order emails, invoice emails, invoice PDFs and the admin order view
- Editable in the admin customer address and order address edit forms

## Configuration

**Stores > Configuration > Loki Checkout > Indian GST > Require GST Number** (`loki_checkout/indian_gst/required`):
when enabled, a GST Number is required for every billing and shipping address in India.

## Installation

```bash
composer require loki-checkout/magento2-indian-gst
bin/magento module:enable LokiCheckout_IndianGst
bin/magento setup:upgrade
bin/magento cache:flush
```

## User stories

The expected behaviour is documented in the `stories/` folder.

## Current status

<!-- badges.test.start -->
<div class="github-pipeline-badges">

![Static Tests](https://img.shields.io/github/actions/workflow/status/LokiCheckout/LokiCheckout_IndianGst/static-tests.yml?label=static-tests)
![Unit Tests](https://img.shields.io/github/actions/workflow/status/LokiCheckout/LokiCheckout_IndianGst/unit-tests.yml?label=unit-tests)
![Integration Tests](https://img.shields.io/github/actions/workflow/status/LokiCheckout/LokiCheckout_IndianGst/integration-tests.yml?label=integration-tests)
![Playwright](https://img.shields.io/github/actions/workflow/status/LokiCheckout/LokiCheckout_IndianGst/playwright.yml?label=playwright)
![DI Compilation](https://img.shields.io/github/actions/workflow/status/LokiCheckout/LokiCheckout_IndianGst/compile.yml?label=compile)

</div>
<!-- badges.test.end -->
