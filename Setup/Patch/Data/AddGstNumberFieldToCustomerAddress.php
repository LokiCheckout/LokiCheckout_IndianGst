<?php
declare(strict_types=1);

namespace LokiCheckout\IndianGst\Setup\Patch\Data;

use Magento\Customer\Api\AddressMetadataInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Customer\Model\ResourceModel\Attribute as AttributeResourceModel;

class AddGstNumberFieldToCustomerAddress implements DataPatchInterface
{
    public function __construct(
        private EavSetupFactory $eavSetupFactory,
        private ModuleDataSetupInterface $moduleDataSetup,
        private EavConfig $eavConfig,
        private AttributeResourceModel $attributeResourceModel
    ) {
    }

    public static function getDependencies()
    {
        return [];
    }

    public function getAliases()
    {
        return [];
    }

    public function apply()
    {
        $attributeCode = 'gst_number';
        $attributeLabel = 'GST Number';

        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);

        $eavSetup->addAttribute(
            'customer_address',
            $attributeCode,
            [
                'input' => 'text',
                'is_visible_in_grid' => false,
                'visible' => true,
                'user_defined' => true,
                'is_filterable_in_grid' => false,
                'system' => false,
                'label' => $attributeLabel,
                'position' => 200,
                'type' => 'varchar',
                'is_used_in_grid' => false,
                'required' => false,
            ]
        );

        $attribute = $this->eavConfig->getAttribute('customer_address', $attributeCode);
        $attribute->setData(
            'used_in_forms',
            [
                'adminhtml_customer',
                'adminhtml_checkout',
                'adminhtml_customer_address',
                'customer_account_edit',
                'customer_address_edit',
                'customer_register_address',
            ]
        );

        $this->attributeResourceModel->save($attribute);

        $eavSetup->addAttributeToGroup(
            'customer_address',
            AddressMetadataInterface::ATTRIBUTE_SET_ID_ADDRESS,
            AddressMetadataInterface::ATTRIBUTE_SET_ID_ADDRESS,
            $attributeCode,
            null
        );

        return $this;
    }
}
