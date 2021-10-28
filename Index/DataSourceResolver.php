<?php
declare(strict_types=1);

namespace TadeuRodrigues\Solr\Index;

use TadeuRodrigues\Solr\Api\Index\DataSourceResolverInterface;
use TadeuRodrigues\Solr\Api\Index\DataSourceInterface;

class DataSourceResolver implements DataSourceResolverInterface
{
    /**
     * @var array
     */
    private array $datasources;

    /**
     * @param array $datasources
     */
    public function __construct(array $datasources = [])
    {
        $this->datasources = $datasources;
    }

    /**
     * @inheritDoc
     */
    public function getDataSources(string $indexName): array
    {
        $sources = [];

        if (isset($this->datasources[$indexName])) {
            foreach ($this->datasources[$indexName] as $name => $datasource) {
                if (!$datasource instanceof DataSourceInterface) {
                    throw new \InvalidArgumentException(__('DataSource must implement '));
                }
                $sources[$name] = $datasource;
            }
        }

        return $sources;
    }
}
