<?php
declare(strict_types=1);

namespace TadeuRodrigues\Solr\SearchAdapter;

use Magento\Framework\ObjectManager\ObjectManager;
use Magento\Framework\Search\Response\Aggregation;

class AggregationFactory
{
    /**
     * @var ObjectManager
     */
    protected ObjectManager $objectManager;

    /**
     * @param ObjectManager $objectManager
     */
    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * @param array $rawAggregation
     * @return Aggregation
     */
    public function create(array $rawAggregation): Aggregation
    {
        // TODO: implementar aggregation module-elasticsearch/SearchAdapter/AggregationFactory.php:40
        $buckets = [
            ['name' => '', 'values' => '']
        ];

        return $this->objectManager->create(
            Aggregation::class,
            ['buckets' => $buckets]
        );
    }
}
