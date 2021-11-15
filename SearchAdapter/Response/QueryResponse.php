<?php
declare(strict_types=1);

namespace TadeuRodrigues\Solr\SearchAdapter\Response;

use Magento\Framework\Search\ResponseInterface;
use Magento\Framework\Api\Search\AggregationInterface;
use TadeuRodrigues\Solr\SearchAdapter\Response\DocumentFactory;
use TadeuRodrigues\Solr\SearchAdapter\Response\Document;
use TadeuRodrigues\Solr\SearchAdapter\Response\AggregationFactory;

class QueryResponse implements ResponseInterface
{
    /**
     * @var Document[]
     */
    protected array $documents = [];

    /**
     * @var AggregationInterface
     */
    protected AggregationInterface $aggregations;

    /**
     * @var int
     */
    protected int $count;

    /**
     * @param DocumentFactory $documentFactory
     * @param AggregationFactory $aggregationFactory
     * @param array $searchResponse
     */
    public function __construct(
        DocumentFactory $documentFactory,
        AggregationFactory $aggregationFactory,
        array $searchResponse
    ) {
        $this->prepareDocuments($searchResponse, $documentFactory);
        $this->prepareAggregations($searchResponse, $aggregationFactory);
    }

    /**
     * @inheritDoc
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->documents);
    }

    /**
     * @inheritDoc
     */
    public function count()
    {
        return $this->count;
    }

    /**
     * @inheritDoc
     */
    public function getAggregations()
    {
        return $this->aggregations;
    }

    /**
     * @param array $searchResponse
     * @param DocumentFactory $documentFactory
     * @return void
     */
    private function prepareDocuments(array $searchResponse, DocumentFactory $documentFactory)
    {
        $this->documents = [];

        if (isset($searchResponse['documents'])) {
            $documents = $searchResponse['documents'];

            foreach ($documents as $document) {
                $this->documents[] = $documentFactory->create($document);
            }

            $this->count = $searchResponse['total'];
        }
    }

    /**
     * @param array $searchResponse
     * @param AggregationFactory $aggregationFactory
     * @return void
     */
    private function prepareAggregations(array $searchResponse, AggregationFactory $aggregationFactory)
    {
        $aggregations = [];

        if (isset($searchResponse['aggregations'])) {
            $aggregations = $searchResponse['aggregations'];
        }

        $this->aggregations = $aggregationFactory->create($aggregations);
    }
}
