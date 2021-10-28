<?php
declare(strict_types=1);

namespace TadeuRodrigues\Solr\Model\Adapter;

interface BatchDataMapperInterface
{
    /**
     * @param array $documentData
     * @param int $storeId
     * @param array $context
     * @return array
     */
    public function map(array $documentData, int $storeId, array $context = []): array;
}
