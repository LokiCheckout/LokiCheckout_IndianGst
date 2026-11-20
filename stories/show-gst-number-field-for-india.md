%title: Show the GST Number field for Indian addresses
%status: complete
---

**As a** customer buying from India,  
**I want to** enter the GST Number (GSTIN) of my business in the checkout address forms,  
**So that** the shop can issue a GST-compliant invoice to my business.

## Acceptance Criteria

### when-shipping-country-is-india

**Given** I am in the checkout  
**When** I select India as the country of my shipping address  
**Then** an optional "GST Number" field is shown in the shipping address form  
**And** the field shows the placeholder `22AAAAA0000A1ZC`  
**And** I do not need to reload the page for the field to appear.

### when-shipping-country-is-not-india

**Given** I am in the checkout  
**When** the country of my shipping address is not India  
**Then** no "GST Number" field is shown in the shipping address form.

### when-billing-country-is-india

**Given** I am in the checkout  
**And** I use a separate billing address  
**When** I select India as the country of my billing address  
**Then** a "GST Number" field is shown in the billing address form  
**And** this does not depend on the country of my shipping address.

### when-billing-country-is-not-india

**Given** I use a separate billing address  
**And** the country of my shipping address is India  
**When** the country of my billing address is not India  
**Then** no "GST Number" field is shown in the billing address form.

### when-country-is-switched-away-from-india

**Given** I selected India as country  
**And** I entered a GST Number  
**When** I switch the country of that address to another country  
**Then** the "GST Number" field disappears  
**And** the entered GST Number is removed from that address.
