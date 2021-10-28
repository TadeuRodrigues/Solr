<?php
declare(strict_types=1);

namespace TadeuRodrigues\Solr\SearchAdapter;

use Magento\Framework\Search\AdapterInterface;
use Magento\Framework\Search\RequestInterface;
use Magento\Framework\Search\Response\QueryResponse;
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
     * @var ResponseFactory
     */
    protected ResponseFactory $responseFactory;

    /**
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * @param ConnectionManager $connectionManager
     * @param ResponseFactory $responseFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        ConnectionManager $connectionManager,
        ResponseFactory $responseFactory,
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
        try {
            $response = $this->doSearch($request);
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
        }

        // TODO: implementar query module-elasticsearch-7/SearchAdapter/Adapter.php:109
        $queryResponse = $this->responseFactory->create(
            [
                'documents' => $response['documents'],
                'aggregations' => [],
                'total' => $response['total']
            ]
        );

        return $queryResponse;
    }

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

    private function prepareResponse(string $resultset): array
    {
        $resultObj = json_decode($resultset);

        return [
            'documents' => $resultObj->response->docs,
            'total' => $resultObj->response->numFound
        ];
    }

    private function prepareSearchQuery(RequestInterface $request)
    {

    }
}
