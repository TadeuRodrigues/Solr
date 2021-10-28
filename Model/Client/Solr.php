<?php
declare(strict_types=1);

namespace TadeuRodrigues\Solr\Model\Client;

use Magento\AdvancedSearch\Model\Client\ClientInterface;
use Magento\Framework\Exception\LocalizedException;
use Solarium\Core\Client\Client;
use Solarium\Core\Query\Result\ResultInterface;
use Solarium\Core\Client\Adapter\Http as Adapter;
use Solarium\QueryType\Server\CoreAdmin\Result\StatusResult;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Psr\Log\LoggerInterface;

class Solr implements ClientInterface
{
    /**
     * @var array
     */
    private array $clientOptions;

    /**
     * @var Client[]
     */
    protected array $client;

    /**
     * @var
     */
    protected $pingResult;

    /**
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * @param LoggerInterface $logger
     * @param array $options
     * @param null $solrClient
     * @throws LocalizedException
     */
    public function __construct(
        LoggerInterface $logger,
        $options = [],
        $solrClient = null
    ) {
        $this->logger = $logger;
        if (empty($options['hostname'])
            || ((!empty($options['enableAuth']) && ($options['enableAuth'] == 1))
                && (empty($options['username']) || empty($options['password'])))
        ) {
            throw new LocalizedException(
                __('The search failed because of a search engine misconfiguration.')
            );
        }
        // phpstan:ignore
        if ($solrClient instanceof Client) {
            $this->client[getmypid()] = $solrClient;
        }
        $this->clientOptions = $options;
    }

    /**
     * @inheritDoc
     */
    public function testConnection()
    {
        return $this->ping();
    }


    /**
     * @return ResultInterface
     */
    public function ping(): ResultInterface
    {
        if ($this->pingResult == null) {
            $pingQuery = $this->getSolrClient()->createPing();
            $this->pingResult = $this->getSolrClient()->execute($pingQuery);
        }

        return $this->pingResult;
    }

    /**
     * @return array
     */
    private function buildSolrConfig(): array
    {
        return [
            'endpoint' => [
                'localhost' => [
                    'host' => $this->clientOptions['hostname'],
                    'port' => $this->clientOptions['port'],
                    'path' => '/',
                    'core' => 'gettingstarted',
                ]
            ]
        ];
    }

    /**
     * @return Client
     */
    public function getSolrClient(): Client
    {
        $pid = getmypid();
        if (!isset($this->client[$pid])) {
            $config = $this->buildSolrConfig();
            // create an HTTP adapter instance
            $adapter = new Adapter();
            $this->client[$pid] = new Client($adapter, new EventDispatcher, $config);
        }

        return $this->client[$pid];
    }

    /**
     * @param string $index
     * @return void
     */
    public function deleteIndex(string $index): void
    {
        $coreAdminQuery = $this->getSolrClient()->createCoreAdmin();

        $unloadAction = $coreAdminQuery->createUnload();
        $unloadAction->setCore($index)->setDeleteIndex(true);
        $coreAdminQuery->setAction($unloadAction);

        try {
            $response = $this->getSolrClient()->coreAdmin($coreAdminQuery);
            $result = $response->getStatusResult();
        } catch (\Exception $e) {
            $message = json_decode($e->getBody());
            $this->logger->notice($message->error->msg);
        }
    }

    /**
     * @param string $index
     * @return void
     */
    public function createIndex(string $index): void
    {
        $coreAdminQuery = $this->getSolrClient()->createCoreAdmin();

        $createAction = $coreAdminQuery->createCreate()->setCore($index);
        $coreAdminQuery->setAction($createAction);

        try {
            $response = $this->getSolrClient()->coreAdmin($coreAdminQuery);
            $result = $response->getStatusResult();
        } catch (\Exception $e) {
            $message = json_decode($e->getBody());
            $this->logger->critical($message->error->msg);
        }

        $this->logger->debug($result->getResponse()->getStatusMessage());
    }

    /**
     * @param array $documentIds
     * @param $storeId
     * @return void
     */
    public function deleteDocs(array $documentIds, $storeId = null)
    {
        $queries = [];
        foreach ($documentIds as $documentsId) {
          $queries[] = "id:" . $documentsId;
        }

        $client = $this->getSolrClient();
        $update = $this->getSolrClient()->createUpdate();
        $update->addDeleteQueries($queries);
        $update->addCommit();

        $result = null;
        try {
            $result = $client->update($update);
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
        }

        $this->logger->debug($result->getResponse()->getStatusMessage());
    }

    /**
     * @param array $documents
     * @param $storeId
     * @return void
     */
    public function addDocs(array $documents, $storeId = null)
    {
        $client = $this->getSolrClient();
        $update = $this->getSolrClient()->createUpdate();

        $doc = $update->createDocument($documents);
        $update->setDocumentClass();

        $doc->id = 1;
        $doc->title = 'exemplo';
        $doc->qty = 10;
        $doc->price = 10.0;

        $update->addDocument($doc)->addCommit();
        $result = $client->update($update);
    }

    /**
     * @param array $query
     * @return string
     */
    public function query(array $query): string
    {
        $client = $this->getSolrClient();
        $query = $client->createSelect();

        $facetSet = $query->getFacetSet();
        $facetSet->createFacetQuery('stock')->setQuery('is_in_stock: true');

        $query->setQuery('*:*');
        $resultset = $client->select($query)->getResponse();

        return $resultset->getBody();
    }
}
