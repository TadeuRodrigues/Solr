<?php
declare(strict_types=1);

namespace TadeuRodrigues\Solr\Model\Indexer;

use Magento\Framework\Indexer\IndexStructureInterface;
use TadeuRodrigues\Solr\Model\Adapter\Solr as SolrAdapter;
use Magento\Framework\App\ScopeResolverInterface;

class IndexStructure implements IndexStructureInterface
{
    /**
     * @var SolrAdapter
     */
    protected SolrAdapter $adapter;

    /**
     * @var ScopeResolverInterface
     */
    protected ScopeResolverInterface $scopeResolver;

    /**
     * @param SolrAdapter $adapter
     * @param ScopeResolverInterface $scopeResolver
     */
    public function __construct(
        SolrAdapter $adapter,
        ScopeResolverInterface $scopeResolver
    ) {
        $this->adapter = $adapter;
        $this->scopeResolver = $scopeResolver;
    }

    /**
     * @inheritDoc
     */
    public function delete($index, array $dimensions = [])
    {
        $dimension = current($dimensions);
        $scopeId = $this->scopeResolver->getScope((int)$dimension->getValue())->getId();
        $this->adapter->cleanIndex($scopeId, $index);
    }

    /**
     * @inheritDoc
     */
    public function create($index, array $fields, array $dimensions = [])
    {
        $dimension = current($dimensions);
        $scopeId = $this->scopeResolver->getScope((int)$dimension->getValue())->getId();
        $this->adapter->checkIndex($scopeId);
    }
}
