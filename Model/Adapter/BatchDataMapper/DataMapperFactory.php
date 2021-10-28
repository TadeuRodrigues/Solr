<?php
declare(strict_types=1);

namespace TadeuRodrigues\Solr\Model\Adapter\BatchDataMapper;

use Magento\Framework\ObjectManagerInterface;
use TadeuRodrigues\Solr\Model\Adapter\BatchDataMapperInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\ConfigurationMismatchException;


class DataMapperFactory
{
    /**
     * @var ObjectManagerInterface
     */
    protected ObjectManagerInterface $objectManager;

    /**
     * @var string[]
     */
    protected array $dataMappers;

    /**
     * @param ObjectManagerInterface $objectManager
     * @param array $dataMappers
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        array $dataMappers = []
    ) {
        $this->objectManager = $objectManager;
        $this->dataMappers = $dataMappers;
    }

    /**
     * @param string $entityType
     * @return BatchDataMapperInterface
     * @throws ConfigurationMismatchException
     * @throws NoSuchEntityException
     */
    public function create(string $entityType): BatchDataMapperInterface
    {
        if (!isset($this->dataMappers[$entityType])) {
            throw new NoSuchEntityException(
                __(
                    'There is no such mapper "%1" for interface %2',
                    $entityType,
                    BatchDataMapperInterface::class
                ));
        }

        $dataMapperClass = $this->dataMappers[$entityType];
        $dataMapperEntity = $this->objectManager->create($dataMapperClass);
        if (!$dataMapperEntity instanceof BatchDataMapperInterface) {
            throw new ConfigurationMismatchException(
                __(
                    'Data mapper "%1" must implement interface %2',
                    $dataMapperClass,
                    BatchDataMapperInterface::class
                )
            );
        }

        return $dataMapperEntity;
    }
}
