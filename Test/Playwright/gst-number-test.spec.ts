import {Field, PaymentMethod, PlaceOrderButton, ShippingMethod, Sidebar, SuccessPage} from '@loki-checkout/checkout-objects';
import {setupCheckout} from '@loki/setup-checkout';
import {test, expect} from '@loki/test';
import merge from '@loki/util/merge';
import type {Page} from '@playwright/test';
import moduleConfig from './config/config';

const VALID_GST = '27AAPFU0939F1ZV';

const requiredConfig = merge(moduleConfig, {
    config: {
        'loki_checkout/indian_gst/required': 1,
    }
});

async function waitForLokiAjax(page: Page, action: () => Promise<void>) {
    const ajaxDone = page.evaluate(() => new Promise(resolve => {
        document.addEventListener('loki-components.ajax.done', resolve, {once: true});
        setTimeout(resolve, 10000);
    }));

    await action();
    await ajaxDone;
}

async function switchToIndia(page: Page, addressType: string = 'shipping') {
    await new Field(page, addressType + '.country_id').fill('IN');
    await new Field(page, addressType + '.street0').fill('123 Marine Drive');
    await new Field(page, addressType + '.region').fill('Maharashtra');
    await new Field(page, addressType + '.city').fill('Mumbai');
    await new Field(page, addressType + '.postcode').fill('400001');
}

async function useSeparateBillingAddress(page: Page) {
    const checkbox = page.locator('#loki-checkout-billing-address-shipping-as-billing-field');
    await waitForLokiAjax(page, () => checkbox.uncheck());
    await expect(new Field(page, 'billing.firstname').getLocator()).toBeVisible();

    const fields = {
        'billing.firstname': 'Asha',
        'billing.lastname': 'Patel',
        'billing.telephone': '9876543210',
    };

    for (const [fieldName, fieldValue] of Object.entries(fields)) {
        await new Field(page, fieldName).fill(fieldValue);
    }
}

test.describe('Indian GST Number field', () => {
    test('should not show the GST field when the country is not India', async ({page, context}) => {
        await setupCheckout(page, context, moduleConfig);

        await expect(new Field(page, 'shipping.gst_number').getLocator()).toHaveCount(0);
    });

    test('should show the GST field without reload when switching to India', async ({page, context}) => {
        await setupCheckout(page, context, moduleConfig);
        await switchToIndia(page);

        const gstField = new Field(page, 'shipping.gst_number');
        await expect(gstField.getLocator()).toBeVisible();
        await expect(gstField.getLocator()).toHaveAttribute('placeholder', '22AAAAA0000A1ZC');
        await expect(gstField.getLocator()).not.toHaveAttribute('required');
    });

    test('should mark an invalid GST format as invalid', async ({page, context}) => {
        await setupCheckout(page, context, moduleConfig);
        await switchToIndia(page);

        const gstField = new Field(page, 'shipping.gst_number');
        await gstField.fill('INVALIDGST1234');

        await expect(gstField.getLocator()).toHaveAttribute('aria-invalid', 'true');
        await expect(page.getByText('Invalid GST Number format. Expected format: 22AAAAA0000A1ZC')).toBeVisible();
    });

    test('should mark a GST number with a wrong check character as invalid', async ({page, context}) => {
        await setupCheckout(page, context, moduleConfig);
        await switchToIndia(page);

        const gstField = new Field(page, 'shipping.gst_number');
        await gstField.fill('27AAPFU0939F1Z5');

        await expect(gstField.getLocator()).toHaveAttribute('aria-invalid', 'true');
        await expect(page.getByText('Invalid GST Number: The check character does not match')).toBeVisible();
    });

    test('should mark a GST number with a wrong state code as invalid', async ({page, context}) => {
        await setupCheckout(page, context, moduleConfig);
        await switchToIndia(page);

        const gstField = new Field(page, 'shipping.gst_number');
        await gstField.fill('39AAPFU0939F1ZV');

        await expect(gstField.getLocator()).toHaveAttribute('aria-invalid', 'true');
        await expect(page.getByText('Invalid GST Number: The first two digits are not a valid GST state code')).toBeVisible();
    });

    test('should accept lowercase letters and spaces and store them normalized', async ({page, context}) => {
        await setupCheckout(page, context, moduleConfig);
        await switchToIndia(page);

        const gstField = new Field(page, 'shipping.gst_number');
        await gstField.fill('27 aapfu 0939f1zv');
        await expect(gstField.getLocator()).not.toHaveAttribute('aria-invalid', 'true');

        await page.reload();
        await expect(new Field(page, 'shipping.gst_number').getLocator()).toHaveValue(VALID_GST);
    });

    test('should show the GST number in the checkout summary', async ({page, context}) => {
        await setupCheckout(page, context, moduleConfig);
        await switchToIndia(page);
        await new Field(page, 'shipping.gst_number').fill(VALID_GST);

        await new Sidebar(page).containsText('GST: ' + VALID_GST);
    });

    test('should hide and clear the GST number when switching away from India', async ({page, context}) => {
        await setupCheckout(page, context, moduleConfig);
        await switchToIndia(page);
        await new Field(page, 'shipping.gst_number').fill(VALID_GST);

        await new Field(page, 'shipping.country_id').fill('NL');
        await expect(new Field(page, 'shipping.gst_number').getLocator()).toHaveCount(0);
        await expect(new Sidebar(page).getLocator()).not.toContainText('GST: ' + VALID_GST);

        await new Field(page, 'shipping.country_id').fill('IN');
        await expect(new Field(page, 'shipping.gst_number').getLocator()).toBeVisible();
        await expect(new Field(page, 'shipping.gst_number').getLocator()).toHaveValue('');
    });

    test('should show the GST field for an Indian billing address only', async ({page, context}) => {
        await setupCheckout(page, context, moduleConfig);
        await useSeparateBillingAddress(page);
        await switchToIndia(page, 'billing');

        await expect(new Field(page, 'billing.gst_number').getLocator()).toBeVisible();
        await expect(new Field(page, 'shipping.gst_number').getLocator()).toHaveCount(0);
    });

    test('should accept a valid GST number and place the order', async ({page, context}) => {
        await setupCheckout(page, context, moduleConfig);
        await switchToIndia(page);

        const gstField = new Field(page, 'shipping.gst_number');
        await gstField.fill(VALID_GST);
        await expect(gstField.getLocator()).not.toHaveAttribute('aria-invalid', 'true');

        await new ShippingMethod(page, 'flatrate_flatrate').select();
        await new PaymentMethod(page, 'checkmo').select();
        await new PlaceOrderButton(page).click();

        await new SuccessPage(page).expectToBeLoaded();
    });
});

test.describe('Required Indian GST Number field', () => {
    test('should mark the GST field as required when configured', async ({page, context}) => {
        await setupCheckout(page, context, requiredConfig);
        await switchToIndia(page);

        const gstField = new Field(page, 'shipping.gst_number');
        await expect(gstField.getLocator()).toHaveAttribute('required');
    });

    test('should not require a GST number outside India', async ({page, context}) => {
        await setupCheckout(page, context, requiredConfig);

        await expect(new Field(page, 'shipping.gst_number').getLocator()).toHaveCount(0);

        await new ShippingMethod(page, 'flatrate_flatrate').select();
        await new PaymentMethod(page, 'checkmo').select();
        await new PlaceOrderButton(page).click();

        await new SuccessPage(page).expectToBeLoaded();
    });
});
