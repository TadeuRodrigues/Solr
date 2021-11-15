<?php
declare(strict_types=1);

namespace TadeuRodrigues\Solr\SearchAdapter\Response;

use Magento\Framework\Search\Response\AggregationFactory as SearchAggregationFactory;
use Magento\Framework\Search\Response\BucketFactory;
use Magento\Framework\Search\Response\Aggregation\Value;
use Magento\Framework\Search\Response\Aggregation\ValueFactory;

class AggregationFactory
{
    /**
     * @var SearchAggregationFactory
     */
    protected SearchAggregationFactory $aggregationFactory;

    /**
     * @var BucketFactory
     */
    protected BucketFactory $bucketFactory;

    /**
     * @var ValueFactory
     */
    protected ValueFactory $valueFactory;

    /**
     * @param SearchAggregationFactory $aggregationFactory
     * @param BucketFactory $bucketFactory
     * @param ValueFactory $valueFactory
     */
    public function __construct(
        SearchAggregationFactory $aggregationFactory,
        BucketFactory $bucketFactory,
        ValueFactory $valueFactory
    ) {
        $this->aggregationFactory = $aggregationFactory;
        $this->bucketFactory = $bucketFactory;
        $this->valueFactory = $valueFactory;
    }

    public function create(array $rawAggregations)
    {
        $buckets = $this->getBuckets($rawAggregations);

        return $this->aggregationFactory->create(['buckets' => $buckets]);
    }

    /**
     * @param array $rawAggregations
     * @return array
     */
    private function getBuckets(array $rawAggregations): array
    {
        $buckets = [];

        foreach ($rawAggregations as $bucketName => $rawBucket) {
            while (isset($rawBucket[$bucketName])) {
                $rawBucket = $rawBucket[$bucketName];
            }

            // TODO: chamar getBucketValues para o value
            $bucketParams = ['name' => $bucketName, 'values' => $this->valueFactory->create(['value' => $rawBucket])];
            $buckets[$bucketName] = $this->bucketFactory->create($bucketParams);
        }

        return $buckets;
    }

    private function getBucketValues(array $rawBucket): array
    {
        return [];
    }
}
