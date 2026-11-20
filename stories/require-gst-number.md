%title: Require the GST Number
%status: complete
---

**As a** merchant selling to businesses in India,  
**I want to** make the GST Number field required,  
**So that** every order with an Indian address carries a GSTIN for invoicing.

## Acceptance Criteria

### when-configuration-is-opened

**Given** I am logged in to the Magento admin  
**When** I open **Stores > Configuration > Loki Checkout > Indian GST**  
**Then** the setting "Require GST Number" is shown  
**And** I can choose "Yes" or "No"  
**And** I can set it on default, website and store view level.

### when-nothing-is-configured

**Given** the setting has not been changed  
**When** I open the checkout with an Indian address  
**Then** the "GST Number" field is optional.

### when-gst-number-is-required

**Given** "Require GST Number" is set to "Yes"  
**When** I open the checkout with an Indian address  
**Then** the "GST Number" field is marked as required  
**And** I can not continue with an empty GST Number.

### when-gst-number-is-required-and-country-is-not-india

**Given** "Require GST Number" is set to "Yes"  
**When** the country of the address is not India  
**Then** no "GST Number" field is shown  
**And** I can place the order without a GST Number.
