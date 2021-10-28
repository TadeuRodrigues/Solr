<?php
declare(strict_types=1);

namespace TadeuRodrigues\Solr\Model\Client;

use Magento\AdvancedSearch\Model\Client\ClientFactoryInterface;
use Magento\AdvancedSearch\Model\Client\ClientResolver;

class ClientFactoryProxy implements ClientFactoryInterface
{
    /**
     * @var ClientResolver
     */
    protected ClientResolver $clientResolver;

    /**
     * @var ClientFactoryInterface[]
     */
    protected array $clientFactories;

    /**
     * @param ClientResolver $clientResolver
     * @param array $clientFactories
     */
    public function __construct(
        ClientResolver $clientResolver,
        array $clientFactories
    ) {
        $this->clientResolver = $clientResolver;
        $this->clientFactories = $clientFactories;
    }

    /**
     * @return ClientFactoryInterface
     */
    public function getClientFactory()
    {
        return $this->clientFactories[$this->clientResolver->getCurrentEngine()];
    }

    /**
     * @inheritDoc
     */
    public function create(array $options = [])
    {
        return $this->getClientFactory()->create($options);
    }
}
