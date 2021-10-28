<?php
declare(strict_types=1);

namespace TadeuRodrigues\Solr\Model\Adapter\BatchDataMapper;

use Magento\AdvancedSearch\Model\Adapter\DataMapper\AdditionalFieldsProviderInterface;

class NoneFieldsProvider implements AdditionalFieldsProviderInterface
{
    /**
     * @inheritDoc
     */
    public function getFields(array $productIds, $storeId)
    {
        return [];
    }
}
