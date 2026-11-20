%title: Store the GST Number on the order
%status: complete
---

**As a** merchant,  
**I want to** keep the GST Number of the customer with the billing and shipping address of the order,  
**So that** invoices, emails and the admin show the GSTIN the customer entered.

## Acceptance Criteria

### when-order-is-placed-with-gst-number

**Given** my shipping and billing addresses are in India  
**And** I entered a GST Number for both addresses  
**When** I place the order  
**Then** the order shipping address contains the shipping GST Number  
**And** the order billing address contains the billing GST Number.

### when-order-is-placed-without-gst-number

**Given** my addresses are in India  
**And** I did not enter a GST Number  
**When** I place the order  
**Then** the order addresses have no GST Number.

### when-order-address-is-not-in-india

**Given** an address of my cart still holds a GST Number  
**And** the country of that address is not India  
**When** I place the order  
**Then** the GST Number is not stored on that order address.

### when-order-address-is-displayed

**Given** an order address contains a GST Number  
**When** the address is shown in an order email, invoice email, invoice PDF or the admin order view  
**Then** a line `GST: <number>` is shown below the address.

### when-order-address-is-edited-in-the-admin

**Given** I am logged in to the Magento admin  
**When** I edit an address of an order  
**Then** I can change the "GST Number" of that address.
