<?php
declare(strict_types=1);

namespace TadeuRodrigues\Solr\Model\Adapter\FieldMapper;

use TadeuRodrigues\Solr\Model\Adapter\FieldMapperInterface;
use Magento\Framework\ObjectManagerInterface;
use TadeuRodrigues\Solr\Model\Config;

class FieldMapperResolver implements FieldMapperInterface
{
    /**
     * @var ObjectManagerInterface
     */
    protected ObjectManagerInterface $objectManager;

    /**
     * @var string[]
     */
    protected array $fieldMappers;

    /**
     * Field Mapper instance per entity
     *
     * @var FieldMapperInterface[]
     */
    private array $fieldMapperEntity = [];

    /**
     * @param ObjectManagerInterface $objectManager
     * @param string[] $fieldMappers
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        array $fieldMappers = []
    ) {
        $this->objectManager = $objectManager;
        $this->fieldMappers = $fieldMappers;
    }

    /**
     * @inheritDoc
     * @throws \Exception
     */
    public function getFieldName(string $attributeCode, array $context = []): string
    {
        $entityType = $context['entityType'] ?? Config::SOLR_TYPE_DEFAULT;
        return $this->getEntity($entityType)->getFieldName($attributeCode, $context);
    }

    /**
     * @inheritDoc
     * @throws \Exception
     */
    public function getAllAttributesTypes(array $context = []): array
    {
        $entityType = $context['entityType'] ?? Config::SOLR_TYPE_DEFAULT;
        return $this->getEntity($entityType)->getAllAttributesTypes($context);
    }

    /**
     * @param string $entityType
     * @return FieldMapperInterface
     * @throws \Exception
     */
    private function getEntity(string $entityType): FieldMapperInterface
    {
        if (empty($this->fieldMapperEntity[$entityType])) {
            if (empty($entityType)) {
                // phpcs:ignore Magento2.Exceptions.DirectThrow
                throw new \Exception(
                    'No entity type given'
                );
            }
            if (!isset($this->fieldMappers[$entityType])) {
                throw new \LogicException(
                    'There is no such field mapper: ' . $entityType
                );
            }
            $fieldMapperClass = $this->fieldMappers[$entityType];
            $this->fieldMapperEntity[$entityType] = $this->objectManager->create($fieldMapperClass);
            if (!($this->fieldMapperEntity[$entityType] instanceof FieldMapperInterface)) {
                throw new \InvalidArgumentException(
                    'Field mapper must implement \Magento\Elasticsearch\Model\Adapter\FieldMapperInterface'
                );
            }
        }
        return $this->fieldMapperEntity[$entityType];
    }
}
