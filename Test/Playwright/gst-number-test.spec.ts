import {Field, PaymentMethod, PlaceOrderButton, SuccessPage} from '@loki-checkout/checkout-objects';
import {setupCheckout} from '@loki/setup-checkout';
import {test, expect} from '@loki/test';
import moduleConfig from './config/config';

const VALID_GST = '27AAAPL1234C1Z5';
const INVALID_GST = 'INVALIDGST1234';

async function switchToIndia(page) {
    await new Field(page, 'shipping.country_id').fill('IN');
    await page.reload();

    await new Field(page, 'shipping.region').fill('Maharashtra');
    await new Field(page, 'shipping.city').fill('Mumbai');
    await new Field(page, 'shipping.postcode').fill('400001');
}

test.describe('Indian GST Number field', () => {
    test('should show GST field when country is India', async ({page, context}) => {
        await setupCheckout(page, context, moduleConfig);
        await switchToIndia(page);

        const gstField = new Field(page, 'shipping.gst_number');
        await expect(gstField.getLocator()).toBeVisible();
    });

    test('should mark an invalid GST number as invalid', async ({page, context}) => {
        await setupCheckout(page, context, moduleConfig);
        await switchToIndia(page);

        const gstField = new Field(page, 'shipping.gst_number');
        await gstField.fill(INVALID_GST);

        await expect(gstField.getLocator()).toHaveAttribute('aria-invalid', 'true');
    });

    test('should accept a valid GST number and place the order', async ({page, context}) => {
        await setupCheckout(page, context, moduleConfig);
        await switchToIndia(page);

        const gstField = new Field(page, 'shipping.gst_number');
        await gstField.fill(VALID_GST);
        await expect(gstField.getLocator()).not.toHaveAttribute('aria-invalid', 'true');

        await new PaymentMethod(page, 'checkmo').select();
        await new PlaceOrderButton(page).click();

        await new SuccessPage(page).expectToBeLoaded();
    });

    test('should not show GST field when country is not India', async ({page, context}) => {
        await setupCheckout(page, context, moduleConfig);

        const gstField = new Field(page, 'shipping.gst_number');
        await expect(gstField.getLocator()).toHaveCount(0);
    });
});
