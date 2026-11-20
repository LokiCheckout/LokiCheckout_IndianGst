%title: Reuse the GST Number from the address book
%status: complete
---

**As a** returning customer,  
**I want to** store my GST Number with my address in my address book,  
**So that** I do not need to enter it again for my next order.

## Acceptance Criteria

### when-address-is-saved-in-address-book

**Given** I am logged in  
**And** I entered a new Indian address with a GST Number  
**And** I chose to save the address in my address book  
**When** I place the order  
**Then** the saved customer address contains the GST Number.

### when-saved-address-is-used

**Given** I am logged in  
**And** my address book contains an address with a GST Number  
**When** that address is used in the checkout  
**Then** the "GST Number" field is prefilled with the saved GST Number.

### when-customer-address-is-edited-in-the-admin

**Given** I am logged in to the Magento admin  
**When** I edit an address of a customer  
**Then** I can change the "GST Number" of that address.
