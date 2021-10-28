<?php
declare(strict_types=1);

namespace TadeuRodrigues\Solr\Model\Adapter;

interface FieldMapperInterface
{
    const TYPE_QUERY = 'text';
    const TYPE_SORT = 'sort';
    const TYPE_FILTER = 'default';

    /**
     * @param string $attributeCode
     * @param array $context
     * @return string
     */
    public function getFieldName(string $attributeCode, array $context = []): string;

    /**
     * @param array $context
     * @return array
     */
    public function getAllAttributesTypes(array $context = []): array;
}
