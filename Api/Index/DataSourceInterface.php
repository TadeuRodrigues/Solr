<?php
declare(strict_types=1);

namespace TadeuRodrigues\Solr\Api\Index;

interface DataSourceInterface
{
    /**
     * Append data to a list of documents
     *
     * @param integer $storeId
     * @param array $indexData
     * @return array
     */
    public function addData(int $storeId, array $indexData): array;
}
