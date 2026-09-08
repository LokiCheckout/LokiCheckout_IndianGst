# LokiCheckout_IndianGst

Adds an optional GST Number field to billing and shipping addresses in the Loki Checkout.

## Features

- Optional `gst_number` field on both billing and shipping address forms
- Shown only when the shipping country is India (`IN`)
- Server-side and client-side validation of GSTIN format (`22AAAAA0000A1Z5`)
- Copied to `sales_order_address` on order placement
- Saved to customer address EAV when "save in address book" is checked
- Prefilled from saved customer address on return visits
- Displayed as `GST: <number>` in order emails, invoice emails, and admin order view
- Editable in the admin order address edit form

## Installation

```bash
composer require loki-checkout/magento2-indian-gst
bin/magento module:enable LokiCheckout_IndianGst
bin/magento setup:upgrade
bin/magento cache:flush
```
