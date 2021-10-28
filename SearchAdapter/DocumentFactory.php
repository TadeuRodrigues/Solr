<?php
declare(strict_types=1);

namespace TadeuRodrigues\Solr\SearchAdapter;

use Magento\Framework\Search\EntityMetadata;
use Magento\Framework\Api\Search\Document;
use Magento\Framework\Api\Search\DocumentInterface;
use Magento\Framework\Api\CustomAttributesDataInterface;
use Magento\Framework\Api\AttributeValue;
use Magento\Framework\Api\AttributeInterface;

class DocumentFactory
{
    /**
     * @var EntityMetadata
     */
    protected EntityMetadata $entityMetadata;

    /**
     * @param EntityMetadata $entityMetadata
     */
    public function __construct(EntityMetadata $entityMetadata)
    {
        $this->entityMetadata = $entityMetadata;
    }

    /**
     * @param $rawDocument
     * @return Document
     */
    public function create($rawDocument): Document
    {
        /** @var AttributeValue[] $attributes */
        $attributes = [];
        $documentId = null;
        $entityId = $this->entityMetadata->getEntityId();
        foreach ($rawDocument as $fieldName => $value) {
            if ($fieldName === $entityId) {
                $documentId = current($value);
            } elseif ($fieldName === 'score') {
                $attributes['score'] = new AttributeValue(
                    [
                        AttributeInterface::ATTRIBUTE_CODE => $fieldName,
                        AttributeInterface::VALUE => $value,
                    ]
                );
            }
        }

        // TODO: estudar substituir New por ObjectManager
        return New Document(
            [
                DocumentInterface::ID => $documentId,
                CustomAttributesDataInterface::CUSTOM_ATTRIBUTES => $attributes
            ]
        );
    }
}
