<?php
declare(strict_types=1);

namespace TadeuRodrigues\Solr\Model\Adapter\Document;

class Builder
{
    /**
     * @var array
     */
    private array $fields = [];

    /**
     * @return array
     */
    public function build(): array
    {
        $document = [];

        foreach ($this->fields as $field => $value) {
            $document = $this->addFieldToDocument($document, $field, $value);
        }

        $this->clear();
        return $document;
    }

    /**
     * @return void
     */
    private function clear()
    {
        $this->fields = [];
    }

    /**
     * @param array $document
     * @param string $field
     * @param string|int|float $value
     * @return array
     */
    private function addFieldToDocument(array $document, string $field, $value): array
    {
        if (is_array($value)) {
            if (count($value) == 0) {
                $document = array_merge($document, [$field => $value]);
            } else {
                $fields = [];
                foreach ($value as $key => $val) {
                    $fields[$field][$key] = $val;
                }
                $document = array_merge($document, $fields);
            }
        } else {
            $field = [$field => $value];
            $document = array_merge($document, $field);
        }

        return $document;
    }

    /**
     * @param string $field
     * @param $value
     * @return $this
     */
    public function addField(string $field, $value): Builder
    {
        $this->fields[$field] = $value;
        return $this;
    }

    /**
     * @param array $fields
     * @return $this
     */
    public function addFields(array $fields): Builder
    {
        $this->fields = array_merge($this->fields, $fields);
        return $this;
    }
}
