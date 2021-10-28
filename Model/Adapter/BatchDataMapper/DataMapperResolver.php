<?php
declare(strict_types=1);

namespace TadeuRodrigues\Solr\Model\Adapter\BatchDataMapper;

use TadeuRodrigues\Solr\Model\Adapter\BatchDataMapperInterface;
use TadeuRodrigues\Solr\Model\Config;
use Magento\Framework\Exception\ConfigurationMismatchException;
use Magento\Framework\Exception\NoSuchEntityException;

class DataMapperResolver implements BatchDataMapperInterface
{
    /**
     * @var DataMapperFactory
     */
    protected DataMapperFactory $dataMapperFactory;

    /**
     * @var BatchDataMapperInterface[]
     */
    protected array $dataMapperEntity;

    /**
     * @param DataMapperFactory $dataMapperFactory
     */
    public function __construct(DataMapperFactory $dataMapperFactory) {
        $this->dataMapperFactory = $dataMapperFactory;
    }

    /**
     * @inheritDoc
     */
    public function map(array $documentData, int $storeId, array $context = []): array
    {
        $entityType = $context['entityType'] ?? Config::SOLR_TYPE_DEFAULT;
        return $this->getDataMapper($entityType)->map($documentData, $storeId, $context);
    }

    /**
     * @param string $entityType
     * @return BatchDataMapperInterface
     * @throws ConfigurationMismatchException
     * @throws NoSuchEntityException
     */
    private function getDataMapper(string $entityType): BatchDataMapperInterface
    {
        if (!isset($this->dataMapperEntity[$entityType])) {
            $this->dataMapperEntity[$entityType] = $this->dataMapperFactory->create($entityType);
        }

        return $this->dataMapperEntity[$entityType];
    }
}
