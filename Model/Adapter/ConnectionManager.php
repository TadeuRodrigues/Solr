<?php
declare(strict_types=1);

namespace TadeuRodrigues\Solr\Model\Adapter;

use Magento\AdvancedSearch\Model\Client\ClientOptionsInterface;
use Magento\AdvancedSearch\Model\Client\ClientFactoryInterface;
use TadeuRodrigues\Solr\Model\Client\Solr as Client;
use Psr\Log\LoggerInterface;

class ConnectionManager
{
    /**
     * @var ClientFactoryInterface
     */
    protected ClientFactoryInterface $clientFactory;

    // TODO: precisa setar o tipo
    protected $client;

    /**
     * @var ClientOptionsInterface
     */
    protected ClientOptionsInterface $clientConfig;

    /**
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * @param ClientFactoryInterface $clientFactory
     * @param ClientOptionsInterface $clientConfig
     * @param LoggerInterface $logger
     */
    public function __construct(
        ClientFactoryInterface $clientFactory,
        ClientOptionsInterface $clientConfig,
        LoggerInterface $logger
    ) {
        $this->clientFactory = $clientFactory;
        $this->clientConfig = $clientConfig;
        $this->logger = $logger;
    }

    /**
     * @param array $options
     * @return mixed
     */
    public function getConnection(array $options = [])
    {
        if (!$this->client) {
            $this->connect($options);
        }

        return $this->client;
    }

    /**
     * @param array $options
     * @return void
     */
    public function connect(array $options)
    {
        try {
            $this->client = $this->clientFactory->create($this->clientConfig->prepareClientOptions($options));
        } catch (\Exception $e) {
            $this->logger->critical($e);
            throw new \RuntimeException('Solr client is not set.');
        }
    }
}
