<?php
declare(strict_types=1);

namespace LokiCheckout\IndianGst\Test\Unit\Observer;

use LokiCheckout\IndianGst\Observer\AddGstNumberToAddressFormat;
use Magento\Framework\DataObject;
use Magento\Framework\Event\Observer;
use PHPUnit\Framework\TestCase;

class AddGstNumberToAddressFormatTest extends TestCase
{
    private AddGstNumberToAddressFormat $observer;

    protected function setUp(): void
    {
        $this->observer = new AddGstNumberToAddressFormat();
    }

    public function testAppendsGstNumberToHtmlFormat(): void
    {
        $type = new DataObject([
            'code' => 'html',
            'default_format' => '{{var firstname}} {{var lastname}}',
        ]);

        $event = new \Magento\Framework\Event(['type' => $type]);
        $observer = new Observer(['event' => $event]);

        $this->observer->execute($observer);

        $format = $type->getDefaultFormat();
        $this->assertStringContainsString('{{depend gst_number}}', $format);
        $this->assertStringContainsString('GST: {{var gst_number}}', $format);
        $this->assertStringContainsString('<br/>', $format);
    }

    public function testAppendsGstNumberToTextFormat(): void
    {
        $type = new DataObject([
            'code' => 'text',
            'default_format' => '{{var firstname}} {{var lastname}}',
        ]);

        $event = new \Magento\Framework\Event(['type' => $type]);
        $observer = new Observer(['event' => $event]);

        $this->observer->execute($observer);

        $format = $type->getDefaultFormat();
        $this->assertStringContainsString('{{depend gst_number}}', $format);
        $this->assertStringContainsString('GST: {{var gst_number}}', $format);
        $this->assertStringNotContainsString('<br/>', $format);
    }

    public function testIsIdempotent(): void
    {
        $type = new DataObject([
            'code' => 'html',
            'default_format' => '{{var firstname}}',
        ]);

        $event = new \Magento\Framework\Event(['type' => $type]);
        $observer = new Observer(['event' => $event]);

        $this->observer->execute($observer);
        $firstFormat = $type->getDefaultFormat();

        $this->observer->execute($observer);
        $secondFormat = $type->getDefaultFormat();

        $this->assertSame($firstFormat, $secondFormat);
        $this->assertEquals(1, substr_count($secondFormat, '{{depend gst_number}}'));
    }

    public function testHandlesNullType(): void
    {
        $event = new \Magento\Framework\Event(['type' => null]);
        $observer = new Observer(['event' => $event]);

        $this->observer->execute($observer);

        $this->assertTrue(true);
    }

    public function testAppendsToPdfFormat(): void
    {
        $type = new DataObject([
            'code' => 'pdf',
            'default_format' => '{{var firstname}}',
        ]);

        $event = new \Magento\Framework\Event(['type' => $type]);
        $observer = new Observer(['event' => $event]);

        $this->observer->execute($observer);

        $format = $type->getDefaultFormat();
        $this->assertStringContainsString('GST: {{var gst_number}}', $format);
        $this->assertStringNotContainsString('<br/>', $format);
    }

    public function testAppendsToOnelineFormat(): void
    {
        $type = new DataObject([
            'code' => 'oneline',
            'default_format' => '{{var firstname}}',
        ]);

        $event = new \Magento\Framework\Event(['type' => $type]);
        $observer = new Observer(['event' => $event]);

        $this->observer->execute($observer);

        $format = $type->getDefaultFormat();
        $this->assertStringContainsString('GST: {{var gst_number}}', $format);
        $this->assertStringNotContainsString('<br/>', $format);
    }
}
