<?php
declare(strict_types=1);

namespace TadeuRodrigues\Solr\Model\Product\Indexer\Fulltext\Datasource;

use TadeuRodrigues\Solr\Api\Index\DataSourceInterface;

class AttributeData implements DataSourceInterface
{

    /**
     * @inheritDoc
     */
    public function addData(int $storeId, array $indexData): array
    {
        $productIds = array_keys($indexData);
        $indexData = $this->addAttributeData($storeId, $productIds, $indexData);

        return $indexData;
    }

    private function addAttributeData(int $storeId, array $productIds, array $indexData = []): array
    {

    }
}
