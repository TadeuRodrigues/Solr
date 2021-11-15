<?php
declare(strict_types=1);

namespace TadeuRodrigues\Solr\SearchAdapter\Response;

use Magento\Framework\Api\Search\DocumentInterface;
use Magento\Framework\Search\EntityMetadata;
use Magento\Framework\ObjectManagerInterface;
use TadeuRodrigues\Solr\SearchAdapter\Response\Document;

class DocumentFactory
{
    /**
     * @var ObjectManagerInterface
     */
    protected ObjectManagerInterface $objectManager;

    /**
     * @var EntityMetadata
     */
    protected EntityMetadata $entityMetadata;

    /**
     * @var String
     */
    protected String $instanceName;

    /**
     * @param ObjectManagerInterface $objectManager
     * @param EntityMetadata $entityMetadata
     * @param String $instanceName
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        EntityMetadata $entityMetadata,
        String $instanceName = Document::class
    ) {
        $this->objectManager = $objectManager;
        $this->entityMetadata = $entityMetadata;
        $this->instanceName = $instanceName;
    }

    /**
     * @param array $rawDocument
     * @return DocumentInterface
     */
    public function create(array $rawDocument)
    {
        $entityIdFieldName = $this->entityMetadata->getEntityId();

        // TODO: falta entender pq é usado o id
        //$rawDocument[DocumentInterface::ID] = $rawDocument[$entityIdFieldName];

        return $this->objectManager->create($this->instanceName, ['data' => $rawDocument]);
    }
}
