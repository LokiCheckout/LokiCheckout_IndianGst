<?php
declare(strict_types=1);

namespace LokiCheckout\IndianGst\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Config
{
    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig,
    ) {
    }

    public function isGstNumberRequired(): bool
    {
        return $this->scopeConfig->isSetFlag('loki_checkout/indian_gst/required', ScopeInterface::SCOPE_STORE);
    }
}
