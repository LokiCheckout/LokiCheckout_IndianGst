%title: Validate the GST Number
%status: complete
---

**As a** merchant,  
**I want to** only accept GST Numbers that are structurally valid,  
**So that** typing errors are caught in the checkout instead of on the invoice.

## Acceptance Criteria

### when-gst-number-is-empty

**Given** the GST Number is not required  
**When** I leave the "GST Number" field empty  
**Then** no error is shown  
**And** I can place the order.

### when-gst-number-is-valid

**Given** the "GST Number" field is shown  
**When** I enter a GSTIN with a valid format, state code and check character (like `27AAPFU0939F1ZV`)  
**Then** no error is shown  
**And** I can place the order.

### when-gst-number-has-lowercase-letters-or-spaces

**Given** the "GST Number" field is shown  
**When** I enter a valid GSTIN in lowercase or with spaces (like `27aapfu0939f1zv` or `27 AAPFU 0939F1ZV`)  
**Then** the value is accepted  
**And** it is stored in uppercase without spaces.

### when-gst-number-has-an-invalid-format

**Given** the "GST Number" field is shown  
**When** I enter a value that does not match the 15-character GSTIN format  
**Then** the field is marked as invalid  
**And** the message "Invalid GST Number format. Expected format: 22AAAAA0000A1ZC" is shown.

### when-gst-number-has-an-invalid-state-code

**Given** the "GST Number" field is shown  
**When** I enter a GSTIN whose first two digits are not an Indian GST state code (01 to 38, 96, 97 or 99)  
**Then** the field is marked as invalid  
**And** the message "Invalid GST Number: The first two digits are not a valid GST state code" is shown.

### when-gst-number-has-an-invalid-check-character

**Given** the "GST Number" field is shown  
**When** I enter a GSTIN whose last character does not match the GSTIN checksum  
**Then** the field is marked as invalid  
**And** the message "Invalid GST Number: The check character does not match" is shown.

### when-validation-is-bypassed-in-the-browser

**Given** the browser-side validation is bypassed  
**When** an invalid GST Number is sent to the server  
**Then** the server rejects the value with the same messages.
