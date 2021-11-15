<?php
declare(strict_types=1);

namespace TadeuRodrigues\Solr\Model\Indexer;

use Magento\Framework\Indexer\SaveHandler\IndexerInterface;
use Magento\Framework\Search\Request\Dimension;
use Magento\Framework\App\ScopeResolverInterface;
use Magento\Framework\Indexer\IndexStructureInterface;
use TadeuRodrigues\Solr\Api\Index\DataSourceInterface;
use TadeuRodrigues\Solr\Model\Adapter\Solr as SolrAdapter;
use Magento\Framework\Indexer\SaveHandler\Batch;
use TadeuRodrigues\Solr\Api\Index\DataSourceResolverInterface;

class IndexerHandler implements IndexerInterface
{
    protected const DEFAULT_BATCH_SIZE = 100;

    /**
     * @var IndexStructureInterface
     */
    protected IndexStructureInterface $indexStructure;

    /**
     * @var ScopeResolverInterface
     */
    protected ScopeResolverInterface $scopeResolver;

    /**
     * @var SolrAdapter
     */
    protected SolrAdapter $adapter;

    /**
     * @var DataSourceResolverInterface
     */
    protected DataSourceResolverInterface $dataSourceResolver;

    /**
     * @var Batch
     */
    protected Batch $batch;

    /**
     * @var int|null
     */
    protected int $batchSize;

    /**
     * @var string
     */
    private string $indexName;

    /**
     * @var string
     */
    private string $typeName;

    /**
     * @param IndexStructureInterface $indexStructure
     * @param ScopeResolverInterface $scopeResolver
     * @param SolrAdapter $adapter
     * @param Batch $batch
     * @param DataSourceResolverInterface $dataSourceResolver
     * @param string $indexName
     * @param string $typeName
     * @param int $batchSize
     */
    public function __construct(
        IndexStructureInterface $indexStructure,
        ScopeResolverInterface $scopeResolver,
        SolrAdapter $adapter,
        Batch $batch,
        DataSourceResolverInterface $dataSourceResolver,
        string $indexName = null,
        string $typeName = null,
        $batchSize = self::DEFAULT_BATCH_SIZE
    ) {
        $this->indexStructure = $indexStructure;
        $this->scopeResolver = $scopeResolver;
        $this->adapter = $adapter;
        $this->batch = $batch;
        $this->batchSize = $batchSize;
        $this->indexName = $indexName;
        $this->typeName = $typeName;
        $this->dataSourceResolver = $dataSourceResolver;
    }

    /**
     * @inheritDoc
     */
    public function saveIndex($dimensions, \Traversable $documents)
    {
        // TODO: falta terminar
        $dimension = current($dimensions);
        $scopeId = (int)$this->scopeResolver->getScope((int)$dimension->getValue())->getId();

        foreach ($this->batch->getItems($documents, $this->batchSize) as $batchDocuments) {
            // TODO: captura o produto

            foreach ($this->getDataSources() as $dataSource) {
                if (!empty($batchDocuments)) {
                    $batchDocuments = $dataSource->addData($scopeId, $batchDocuments);
                }
            }

            $this->adapter->addDocs($batchDocuments, $scopeId);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function deleteIndex($dimensions, \Traversable $documents)
    {
        $dimension = current($dimensions);
        $scopeId = $this->scopeResolver->getScope((int)$dimension->getValue())->getId();
        $documentIds = [];
        foreach ($documents as $document) {
            $documentIds[$document] = $document;
        }

        $this->adapter->deleteDocs($documentIds, $scopeId);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function cleanIndex($dimensions)
    {
        // TODO: conferir como criar e remover cores
        //$this->indexStructure->delete($this->getIndexerId(), $dimensions);
        //$this->indexStructure->create($this->getIndexerId(), [], $dimensions);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isAvailable($dimensions = []): bool
    {
        return true;
    }

    /**
     * @return string
     */
    public function getIndexerId(): string
    {
        return "1";
    }

    /**
     * @return DataSourceInterface[]
     */
    private function getDataSources(): array
    {
        return $this->dataSourceResolver->getDataSources($this->indexName);
    }
}
