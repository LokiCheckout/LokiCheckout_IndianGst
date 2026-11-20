<?php
declare(strict_types=1);

namespace LokiCheckout\IndianGst\Component\Checkout\Address\GstNumber;

use LokiCheckout\Core\Component\Base\AddressField\AddressFieldViewModel;
use LokiCheckout\IndianGst\Config\Config;

class GstNumberViewModel extends AddressFieldViewModel
{
    public const COUNTRY_ID = 'IN';

    public function __construct(
        private readonly Config $config,
    ) {
    }

    public function getInputType(): string
    {
        return 'text';
    }

    public function isAllowRendering(): bool
    {
        if (false === $this->isIndianAddress()) {
            return false;
        }

        return parent::isAllowRendering();
    }

    public function isRequired(): bool
    {
        if (false === $this->isIndianAddress()) {
            return false;
        }

        if ($this->config->isGstNumberRequired()) {
            return true;
        }

        return parent::isRequired();
    }

    private function isIndianAddress(): bool
    {
        return $this->getAddressState()->get('country_id') === self::COUNTRY_ID;
    }
}
