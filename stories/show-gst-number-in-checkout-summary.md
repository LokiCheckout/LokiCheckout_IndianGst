%title: Show the GST Number in the checkout summary
%status: complete
---

**As a** customer,  
**I want to** see the GST Number with the address in the checkout summary,  
**So that** I can check it before placing my order.

## Acceptance Criteria

### when-address-has-gst-number

**Given** the country of an address is India  
**And** the address has a GST Number  
**When** the address is shown in the checkout summary  
**Then** a line `GST: <number>` is shown with that address.

### when-address-has-no-gst-number

**Given** an address has no GST Number  
**When** the address is shown in the checkout summary  
**Then** no GST line is shown.

### when-address-is-not-in-india

**Given** an address still holds a GST Number  
**And** the country of that address is not India  
**When** the address is shown in the checkout summary  
**Then** no GST line is shown.
