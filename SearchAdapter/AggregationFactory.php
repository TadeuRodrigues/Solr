<?php
declare(strict_types=1);

namespace TadeuRodrigues\Solr\SearchAdapter;

use Magento\Framework\ObjectManager\ObjectManager;
use Magento\Framework\Search\Response\Aggregation;
use Magento\Framework\Search\Response\Aggregation\Value;
use Magento\Framework\Search\Response\Bucket;

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
        /** @var Bucket[] $buckets */
        $buckets = [
            ['name' => '', 'values' => '']
        ];
        foreach ($rawAggregation as $rawBucketName => $rawBucket) {
            $buckets[$rawBucketName] = $this->objectManager->create(
                Bucket::class,
                [
                    'name' => $rawBucketName,
                    'values' => $this->prepareValues($rawBucket)
                ]
            );
        }

        return $this->objectManager->create(
            Aggregation::class,
            ['buckets' => $buckets]
        );
    }

    /**
     * @param array $values
     * @return Value[]
     */
    private function prepareValues(array $values): array
    {
        /** @var Value[] $valuesObjects */
        $valuesObjects = [];
        foreach ($values as $name => $value) {
            $valuesObjects[] = $this->objectManager->create(
                Value::class,
                [
                    'value' => $name,
                    'metrics' => $value
                ]
            );
        }

        return $valuesObjects;
    }
}
