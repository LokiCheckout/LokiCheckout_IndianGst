import { defineModule } from '@loki/config';

export const IndianGst = defineModule({
    name: 'LokiCheckout_IndianGst',
    paymentMethods: ['checkmo'],
    shippingMethods: ['flatrate'],
});
