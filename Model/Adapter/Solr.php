<?php
declare(strict_types=1);

namespace TadeuRodrigues\Solr\Model\Adapter;

use TadeuRodrigues\Solr\Model\Client\Solr as Client;
use Magento\AdvancedSearch\Model\Client\ClientInterface;
use TadeuRodrigues\Solr\Model\Adapter\ConnectionManager;
use TadeuRodrigues\Solr\Model\Config;
use TadeuRodrigues\Solr\Model\Adapter\BatchDataMapperInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\Exception\LocalizedException;

class Solr
{
    /**
     * @var ClientInterface
     */
    protected ClientInterface $client;

    /**
     * @var Config
     */
    protected Config $clientConfig;

    /**
     * @var ConnectionManager
     */
    protected $connectionManager;

    /**
     * @var BatchDataMapperInterface
     */
    protected BatchDataMapperInterface $batchDocumentDataMapper;

    /**
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * @param ConnectionManager $connectionManager
     * @param Config $clientConfig
     * @param LoggerInterface $logger
     * @param array $options
     * @throws LocalizedException
     */
    public function __construct(
        ConnectionManager $connectionManager,
        Config $clientConfig,
        BatchDataMapperInterface $batchDocumentDataMapper,
        LoggerInterface $logger,
        $options = []
    ) {
        $this->connectionManager = $connectionManager;
        $this->clientConfig = $clientConfig;
        $this->batchDocumentDataMapper = $batchDocumentDataMapper;
        $this->logger = $logger;

        try {
            /** @var Client client */
            $this->client = $this->connectionManager->getConnection($options);
        } catch (\Exception $e) {
            $this->logger->critical($e);
            throw new LocalizedException(__('The search failed because of a search engine misconfiguration'));
        }
    }

    /**
     * removes all documents from solr index
     *
     * @param $storeId
     * @return $this
     */
    public function cleanIndex($storeId)
    {
        $index = 'store_' . $storeId;

        $this->client->deleteIndex($index);
        return $this;
    }

    /**
     * @param $storeId
     * @return $this
     */
    public function checkIndex($storeId = null)
    {
        $index = 'store_' . $storeId;

        $this->client->createIndex($index);
        return $this;
    }

    /**
     * @param array $documentsIds
     * @param $scopeId
     * @return $this
     */
    public function deleteDocs(array $documentsIds, $scopeId = null)
    {
        try {
            $this->client->deleteDocs($documentsIds, $scopeId);
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
        }

        return $this;
    }

    /**
     * @param array $documentData
     * @param $storeId
     * @return array
     */
    public function prepareDocsPerStore(array $documentData, $storeId): array
    {
        $documents = [];
        if (count($documentData)) {
            $documents = $this->batchDocumentDataMapper->map(
                $documentData,
                $storeId
            );
        }

        return $documents;
    }

    /**
     * @param array $documents
     * @param $storeId
     * @return void
     */
    public function addDocs(array $documents, $storeId = null)
    {
        try {
            $this->client->addDocs($documents, $storeId);
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
        }
    }
}
