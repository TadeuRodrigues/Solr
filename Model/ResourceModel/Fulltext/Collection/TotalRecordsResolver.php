<?php
declare(strict_types=1);

namespace TadeuRodrigues\Solr\Model\ResourceModel\Fulltext\Collection;

use Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection\TotalRecordsResolverInterface;
use Magento\Framework\Api\Search\SearchResultInterface;

class TotalRecordsResolver implements TotalRecordsResolverInterface
{
    /**
     * @var SearchResultInterface
     */
    private $searchResult;

    /**
     * @param SearchResultInterface $searchResult
     */
    public function __construct(
        SearchResultInterface $searchResult
    ) {
        $this->searchResult = $searchResult;
    }

    /**
     * @inheritdoc
     */
    public function resolve(): ?int
    {
        return $this->searchResult->getTotalCount();
    }
}
