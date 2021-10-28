<?php
declare(strict_types=1);

namespace TadeuRodrigues\Solr\Model;

use Magento\AdvancedSearch\Model\Client\ClientOptionsInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\AdvancedSearch\Model\Client\ClientResolver;
use Magento\Store\Model\ScopeInterface;

class Config implements ClientOptionsInterface
{
    const SOLR_TYPE_DEFAULT = 'product';

    protected const SOLR_DEFAULT_TIMEOUT = 30;

    /**
     * @var ScopeConfigInterface
     */
    protected ScopeConfigInterface $scopeConfig;

    /**
     * @var ClientResolver
     */
    protected ClientResolver $clientResolver;

    /**
     * @var string|null
     */
    protected string $prefix;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param ClientResolver $clientResolver
     * @param $prefix
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ClientResolver $clientResolver,
        $prefix = null
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->clientResolver = $clientResolver;
        $this->prefix = $prefix ?: $this->clientResolver->getCurrentEngine();
    }

    /**
     * @inheritDoc
     */
    public function prepareClientOptions($options = []): array
    {
        if (empty($options)) {
            $options = [
                'hostname' => $this->getSolrConfigData('server_hostname'),
                'port' => $this->getSolrConfigData('server_port'),
                'index' => $this->getSolrConfigData('index_prefix'),
                'enableAuth' => $this->getSolrConfigData('enable_auth'),
                'username' => $this->getSolrConfigData('username'),
                'password' => $this->getSolrConfigData('password'),
                'timeout' => $this->getSolrConfigData('server_timeout') ? : self::SOLR_DEFAULT_TIMEOUT
            ];
        }
        $allowedOptions = array_merge(array_keys($options));

        return array_filter(
            $options,
            function (string $key) use ($allowedOptions) {
                return in_array($key, $allowedOptions);
            },
            ARRAY_FILTER_USE_KEY
        );
    }

    /**
     * @param string|null $field
     * @param $storeId
     * @return string
     */
    public function getSolrConfigData(string $field, $storeId = null): ?string
    {
        return $this->getSearchConfigData($this->prefix . '_' . $field, $storeId);
    }

    /**
     * @param string|null $field
     * @param $storeId
     * @return string
     */
    public function getSearchConfigData(string $field, $storeId = null): ?string
    {
        $path = 'catalog/search/' . $field;
        return $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE, $storeId);
    }
}
