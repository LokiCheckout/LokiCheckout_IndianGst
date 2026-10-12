import coreConfig from '@loki-checkout/config';

export default {
    ...coreConfig,
    modules: [
        'LokiCheckout_IndianGst',
    ],
    config: {
        ...coreConfig.config,
        'payment/checkmo/active': '1',
    }
};
