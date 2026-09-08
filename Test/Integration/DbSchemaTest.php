<?php
declare(strict_types=1);

namespace LokiCheckout\IndianGst\Test\Integration;

use Magento\TestFramework\Helper\Bootstrap;
use Magento\Framework\DB\Adapter\AdapterInterface;
use PHPUnit\Framework\TestCase;

class DbSchemaTest extends TestCase
{
    private AdapterInterface $connection;

    protected function setUp(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $resource = $objectManager->get(\Magento\Framework\App\ResourceConnection::class);
        $this->connection = $resource->getConnection();
    }

    public function testQuoteAddressHasGstNumberColumn(): void
    {
        $columns = $this->connection->describeTable('quote_address');
        $this->assertArrayHasKey('gst_number', $columns);
        $this->assertSame('varchar', $columns['gst_number']['DATA_TYPE']);
    }

    public function testSalesOrderAddressHasGstNumberColumn(): void
    {
        $columns = $this->connection->describeTable('sales_order_address');
        $this->assertArrayHasKey('gst_number', $columns);
        $this->assertSame('varchar', $columns['gst_number']['DATA_TYPE']);
    }
}
