<?php
declare(strict_types=1);

namespace TadeuRodrigues\Solr\Api\Index;

interface DataSourceResolverInterface
{
    /**
     * Get Data sources of a given index/type combination.
     *
     * @param string $indexName
     * @return DataSourceInterface[]
     */
    public function getDataSources(string $indexName): array;
}
