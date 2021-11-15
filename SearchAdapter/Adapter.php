<?php
declare(strict_types=1);

namespace TadeuRodrigues\Solr\SearchAdapter;

use Magento\Framework\Search\AdapterInterface;
use Magento\Framework\Search\RequestInterface;
use TadeuRodrigues\Solr\SearchAdapter\Response\QueryResponse;
use TadeuRodrigues\Solr\SearchAdapter\Response\QueryResponseFactory;
use TadeuRodrigues\Solr\Model\Adapter\ConnectionManager;
use TadeuRodrigues\Solr\Model\Client\Solr as Client;
use Psr\Log\LoggerInterface;

class Adapter implements AdapterInterface
{
    /**
     * @var ConnectionManager
     */
    protected ConnectionManager $connectionManager;

    /**
     * @var QueryResponseFactory
     */
    protected QueryResponseFactory $responseFactory;

    /**
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * @param ConnectionManager $connectionManager
     * @param QueryResponseFactory $responseFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        ConnectionManager $connectionManager,
        QueryResponseFactory $responseFactory,
        LoggerInterface $logger
    ) {
        $this->connectionManager = $connectionManager;
        $this->responseFactory = $responseFactory;
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public function query(RequestInterface $request)
    {
        // TODO: implementar query module-elasticsearch-7/SearchAdapter/Adapter.php:109
        $searchResponse = [];
        try {
            $searchResponse = $this->doSearch($request);
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
        }

        return $this->responseFactory->create(['searchResponse' => $searchResponse]);
    }

    /**
     * @param RequestInterface $request
     * @return array
     */
    private function doSearch(RequestInterface $request): array
    {
        $searchRequest = [
            'index' => $request->getIndex(),
            'body' => $request
        ];

        /** @var Client $client */
        $client = $this->connectionManager->getConnection();
        $resultset = $client->query($searchRequest);

        return $this->prepareResponse($resultset);
    }

    /**
     * @param string $resultset
     * @return array
     */
    private function prepareResponse(string $resultset): array
    {
        $resultData = json_decode($resultset, true);

        return [
            'documents' => $resultData['response']['docs'],
            'aggregations' => $resultData["facet_counts"]["facet_queries"],
            'total' => $resultData['response']['numFound']
        ];
    }
}
