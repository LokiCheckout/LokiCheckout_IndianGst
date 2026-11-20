<?php
declare(strict_types=1);

namespace LokiCheckout\IndianGst\ViewModel;

use LokiCheckout\Core\Util\Component\AddressStateProvider;
use LokiCheckout\IndianGst\Component\Checkout\Address\GstNumber\GstNumberViewModel;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class GstNumberValue implements ArgumentInterface
{
    public function __construct(
        private AddressStateProvider $addressStateProvider,
    ) {
    }

    public function getValue(AbstractBlock $block): string
    {
        $addressState = $this->addressStateProvider->getAddressState($block);
        if ($addressState->get('country_id') !== GstNumberViewModel::COUNTRY_ID) {
            return '';
        }

        return trim((string)$addressState->get('gst_number'), " \f\n\r\t\v\x00");
    }
}
