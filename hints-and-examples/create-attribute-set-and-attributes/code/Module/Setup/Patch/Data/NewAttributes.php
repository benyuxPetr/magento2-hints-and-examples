<?php

namespace Vendor\Module\Setup\Patch\Data;

use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Vendor\Module\Api\Data\NewAttributesInterface;

class NewAttributes implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory          $eavSetupFactory
    )
    {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [
            \Vendor\Module\Setup\Patch\Data\NewAttributeSet::class
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();

        $attributes = [
            NewAttributesInterface::ATTRIBUTE_CODE_1 => [
                'label' => 'Attr 1',
                'values' => [
                    '1',
                    '2',
                    '3'
                ]
            ],
            NewAttributesInterface::ATTRIBUTE_CODE_2 => [
                'label' => 'Attr 2',
                'values' => [
                    'one',
                    'two'
                ]
            ]
        ];

        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);

        $entityTypeId = $eavSetup->getEntityTypeId(
            \Magento\Catalog\Model\Product::ENTITY
        );

        $attributeSetId = $eavSetup->getAttributeSet(
            $entityTypeId,
            NewAttributesInterface::ATTRIBUTE_SET_NAME,
            'attribute_set_id'
        );

        foreach ($attributes as $attributeCode => $attributeParams) {
            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                $attributeCode,
                [
                    'type' => 'int',
                    'label' => $attributeParams['label'],
                    'input' => 'select',
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                    'visible' => true,
                    'required' => false,
                    'user_defined' => true,
                    'default' => '',
                    'searchable' => false,
                    'filterable' => false,
                    'comparable' => false,
                    'visible_on_front' => false,
                    'used_in_product_listing' => true,
                    'unique' => false,
                    'option' => ['values' => $attributeParams['values']]
                ]
            );

            $attributeId = $eavSetup->getAttributeId(
                $entityTypeId,
                $attributeCode
            );

            $eavSetup->addAttributeToSet(
                $entityTypeId,
                $attributeSetId,
                NewAttributesInterface::ATTRIBUTE_GROUP_NAME,
                $attributeId,
                null
            );
        }

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            NewAttributesInterface::ATTRIBUTE_CODE_3,
            [
                'type' => 'decimal',
                'label' => 'Design Charge',
                'input' => 'price',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'default' => '',
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => true,
                'unique' => false
            ]
        );

        $attributeId = $eavSetup->getAttributeId(
            $entityTypeId,
            NewAttributesInterface::ATTRIBUTE_CODE_3
        );

        $eavSetup->addAttributeToSet(
            $entityTypeId,
            $attributeSetId,
            NewAttributesInterface::ATTRIBUTE_GROUP_NAME,
            $attributeId,
            null
        );

        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }
}


