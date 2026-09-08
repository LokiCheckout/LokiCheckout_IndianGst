import { defineConfig } from '@loki/config';
import { IndianGst } from './modules';

export default defineConfig({
    modules: [IndianGst],
    magerunConfig: {
        storeConfig: {
            'general/country/default': 'IN',
        },
    },
    systemConfig: {
        'loki_checkout/functional_tests/token': 'loki',
    },
});
