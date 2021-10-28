<?php
declare(strict_types=1);

namespace TadeuRodrigues\Solr\SearchAdapter;

use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Search\Response\QueryResponse;
use TadeuRodrigues\Solr\SearchAdapter\DocumentFactory;
use Magento\Framework\Api\Search\Document;
use Magento\Framework\Search\Response\Aggregation;

class ResponseFactory
{
    /**
     * @var ObjectManagerInterface
     */
    protected ObjectManagerInterface $objectManager;

    /**
     * @var DocumentFactory
     */
    protected DocumentFactory $documentFactory;

    /**
     * @var AggregationFactory
     */
    protected AggregationFactory $aggregationFactory;

    /**
     * @param ObjectManagerInterface $objectManager
     * @param DocumentFactory $documentFactory
     * @param AggregationFactory $aggregationFactory
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        DocumentFactory $documentFactory,
        AggregationFactory $aggregationFactory
    ) {
        $this->objectManager = $objectManager;
        $this->documentFactory = $documentFactory;
        $this->aggregationFactory = $aggregationFactory;
    }

    /**
     * @param $response
     * @return void
     */
    public function create($response): QueryResponse
    {
        // TODO: implementar response module-elasticsearch/SearchAdapter/ResponseFactory.php:63
        $documents = [];
        foreach ($response['documents'] as $document) {
            /** @var Document[] $documents */
            $documents[] = $this->documentFactory->create($document);
        }

        $aggregations = $this->aggregationFactory->create([]);

        return $this->objectManager->create(
            QueryResponse::class,
            [
                'documents' => $documents,
                'aggregations' => $aggregations,
                'total' => $response['total']
            ]
        );
    }
}
