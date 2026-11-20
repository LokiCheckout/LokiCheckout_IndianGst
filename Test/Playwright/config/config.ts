import coreConfig from '@loki-checkout/config';

export default {
    ...coreConfig,
    modules: [
        'LokiCheckout_IndianGst',
    ],
    config: {
        ...coreConfig.config,
        'payment/checkmo/active': '1',
        'loki_checkout/indian_gst/required': '0',
    }
};
