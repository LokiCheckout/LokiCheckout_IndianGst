<?php
declare(strict_types=1);

namespace LokiCheckout\IndianGst\Component\Checkout\Address\GstNumber;

use LokiCheckout\Core\Component\Base\AddressField\AddressFieldViewModel;

class GstNumberViewModel extends AddressFieldViewModel
{
    public function getInputType(): string
    {
        return 'text';
    }
}
