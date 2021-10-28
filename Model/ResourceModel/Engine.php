<?php
declare(strict_types=1);

namespace TadeuRodrigues\Solr\Model\ResourceModel;

use Magento\CatalogSearch\Model\ResourceModel\EngineInterface;
use Magento\Catalog\Model\Product\Visibility;

class Engine implements EngineInterface
{
    /**
     * @var Visibility
     */
    protected Visibility $catalogProductVisibility;

    /**
     * @param Visibility $catalogProductVisibility
     */
    public  function __construct(Visibility $catalogProductVisibility)
    {
        $this->catalogProductVisibility = $catalogProductVisibility;
    }


    /**
     * @inheritDoc
     */
    public function getAllowedVisibility(): array
    {
        return $this->catalogProductVisibility->getVisibleInSiteIds();
    }

    /**
     * @inheritDoc
     */
    public function allowAdvancedIndex(): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function processAttributeValue($attribute, $value)
    {
        return $value;
    }

    /**
     * @inheritDoc
     */
    public function prepareEntityIndex($index, $separator = ' ')
    {
        return $index;
    }

    /**
     * @return bool
     */
    public function isAvailable(): bool
    {
        return true;
    }
}
