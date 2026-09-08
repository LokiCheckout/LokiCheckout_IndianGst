import { test, expect } from '@playwright/test';
import { setupCheckout } from '@loki/config';
import { PaymentMethod, ShippingMethod, PlaceOrderButton, SuccessPage } from '@loki/functional-tests';
import { Field } from '@loki/functional-tests';

const VALID_GST = '27AAAPL1234C1Z5';
const INVALID_GST = 'INVALIDGST1234';

test.describe('Indian GST Number field', () => {
    test('should show GST field when country is India', async ({ page }) => {
        await setupCheckout({
            page,
            productSku: 'loki-24-mb01',
            quantity: 1,
            country: 'IN',
            shippingMethod: ShippingMethod.FlatRate,
            paymentMethod: PaymentMethod.CheckMoneyOrder,
            customer: {
                email: 'gst-test@example.com',
                firstname: 'Test',
                lastname: 'User',
                street: ['123 Test Street'],
                city: 'Mumbai',
                postcode: '400001',
                telephone: '9876543210',
                region: 'Maharashtra',
            },
        });

        const gstField = page.locator('[data-type="field"][name="gst_number"]').first();
        await expect(gstField).toBeVisible();
    });

    test('should show validation error for invalid GST number', async ({ page }) => {
        await setupCheckout({
            page,
            productSku: 'loki-24-mb01',
            quantity: 1,
            country: 'IN',
            shippingMethod: ShippingMethod.FlatRate,
            paymentMethod: PaymentMethod.CheckMoneyOrder,
            customer: {
                email: 'gst-test2@example.com',
                firstname: 'Test',
                lastname: 'User',
                street: ['123 Test Street'],
                city: 'Mumbai',
                postcode: '400001',
                telephone: '9876543210',
                region: 'Maharashtra',
            },
        });

        const gstField = page.locator('[data-type="field"][name="gst_number"]').first();
        await gstField.fill(INVALID_GST);
        await gstField.dispatchEvent(new Event('change'));

        await page.waitForTimeout(500);

        const errorMessage = page.locator('.text-error').first();
        await expect(errorMessage).toBeVisible({ timeout: 5000 });
    });

    test('should accept valid GST number and place order', async ({ page }) => {
        await setupCheckout({
            page,
            productSku: 'loki-24-mb01',
            quantity: 1,
            country: 'IN',
            shippingMethod: ShippingMethod.FlatRate,
            paymentMethod: PaymentMethod.CheckMoneyOrder,
            customer: {
                email: 'gst-test3@example.com',
                firstname: 'Test',
                lastname: 'User',
                street: ['123 Test Street'],
                city: 'Mumbai',
                postcode: '400001',
                telephone: '9876543210',
                region: 'Maharashtra',
            },
        });

        const gstField = page.locator('[data-type="field"][name="gst_number"]').first();
        await gstField.fill(VALID_GST);

        await PlaceOrderButton.click(page);
        await SuccessPage.waitFor(page);

        const orderNumber = await SuccessPage.getOrderNumber(page);
        expect(orderNumber).toBeTruthy();
    });

    test('should hide GST field when country is not India', async ({ page }) => {
        await setupCheckout({
            page,
            productSku: 'loki-24-mb01',
            quantity: 1,
            country: 'US',
            shippingMethod: ShippingMethod.FlatRate,
            paymentMethod: PaymentMethod.CheckMoneyOrder,
            customer: {
                email: 'gst-test4@example.com',
                firstname: 'Test',
                lastname: 'User',
                street: ['123 Test Street'],
                city: 'New York',
                postcode: '10001',
                telephone: '2125551234',
                region: 'New York',
            },
        });

        const gstField = page.locator('[data-type="field"][name="gst_number"]').first();
        await expect(gstField).not.toBeVisible({ timeout: 5000 });
    });
});
