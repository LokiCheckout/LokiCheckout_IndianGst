<?php
declare(strict_types=1);

namespace LokiCheckout\IndianGst\Test\Integration;

use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class ModuleTest extends TestCase
{
    public function testModuleIsRegistered(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $moduleList = $objectManager->get(\Magento\Framework\Module\ModuleListInterface::class);
        $this->assertTrue(
            $moduleList->has('LokiCheckout_IndianGst'),
            'Module LokiCheckout_IndianGst is not registered'
        );
    }

    public function testModuleDependenciesAreMet(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $moduleRegistry = $objectManager->get(\Magento\Framework\Module\Registry::class);
        $dependencies = $moduleRegistry->getDependencies('LokiCheckout_IndianGst');

        $dependencyNames = array_column($dependencies, 'name');
        $this->assertContains('LokiCheckout_Core', $dependencyNames);
        $this->assertContains('Magento_Customer', $dependencyNames);
    }
}
