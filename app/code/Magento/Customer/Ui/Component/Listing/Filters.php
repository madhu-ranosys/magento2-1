<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Customer\Ui\Component\Listing;

class Filters extends \Magento\Ui\Component\Filters
{
    /**
     * @param \Magento\Framework\View\Element\UiComponent\ContextInterface $context
     * @param \Magento\Customer\Ui\Component\FilterFactory $filterFactory
     * @param AttributeRepository $attributeRepository
     * @param array $components
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Customer\Ui\Component\FilterFactory $filterFactory,
        \Magento\Customer\Ui\Component\Listing\AttributeRepository $attributeRepository,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $components, $data);
        $this->filterFactory = $filterFactory;
        $this->attributeRepository = $attributeRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function prepare()
    {
        /** @var \Magento\Customer\Api\Data\AttributeMetadataInterface $attribute */
        foreach ($this->attributeRepository->getList() as $newAttributeCode => $attribute) {
            if (!isset($this->components[$attribute->getAttributeCode()])
            ) {
                if ($attribute->getIsUsedInGrid()
                    && $attribute->getIsFilterableInGrid()
                ) {
                    $filter = $this->filterFactory->create($attribute, $this->getContext());
                    $filter->prepare();
                    $this->addComponent($attribute->getAttributeCode(), $filter);
                }
            } elseif ($attribute->getAttributeCode() !== $newAttributeCode) {
                $this->updateFilterConfiguration($attribute, $newAttributeCode);
            }
        }
        parent::prepare();
    }

    /**
     * @param \Magento\Customer\Api\Data\AttributeMetadataInterface $attribute
     * @param string $newAttributeCode
     * @return void
     */
    protected function updateFilterConfiguration($attribute, $newAttributeCode)
    {
        $component = $this->components[$attribute->getAttributeCode()];
        $config  = $component->getData('config');
        $component->setData('name', $newAttributeCode);
        $component->setData('config', array_merge($config, ['dataScope'=> $newAttributeCode]));
        $component->prepare();
    }
}
