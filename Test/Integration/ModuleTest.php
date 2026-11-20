<?php
declare(strict_types=1);

namespace LokiCheckout\IndianGst\Test\Integration;

use Magento\Framework\Module\ModuleListInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use Yireo\IntegrationTestHelper\Test\Integration\Traits\AssertModuleIsEnabled;
use Yireo\IntegrationTestHelper\Test\Integration\Traits\AssertModuleIsRegistered;
use Yireo\IntegrationTestHelper\Test\Integration\Traits\AssertModuleIsRegisteredForReal;

class ModuleTest extends TestCase
{
    use AssertModuleIsRegistered;
    use AssertModuleIsRegisteredForReal;
    use AssertModuleIsEnabled;

    public function testModuleIsEnabled(): void
    {
        $moduleName = 'LokiCheckout_IndianGst';
        $this->assertModuleIsRegistered($moduleName);
        $this->assertModuleIsEnabled($moduleName);

        $module = Bootstrap::getObjectManager()->get(ModuleListInterface::class)->getOne($moduleName);
        $this->assertNotEmpty($module['sequence']);

        foreach ($module['sequence'] as $requiredModule) {
            $this->assertModuleIsRegistered($requiredModule);
            $this->assertModuleIsEnabled($requiredModule);
        }
    }
}
