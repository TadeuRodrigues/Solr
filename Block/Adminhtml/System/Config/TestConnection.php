<?php declare(strict_types=1);

namespace TadeuRodrigues\Solr\Block\Adminhtml\System\Config;

use Magento\AdvancedSearch\Block\Adminhtml\System\Config\TestConnection as TestConnectionBase;

/**
 * Solr test connection block
 */
class TestConnection extends TestConnectionBase
{

    /**
     * @return array
     */
    protected function _getFieldMapping() : array
    {
        $fields = [
            'engine' => 'catalog_search_engine',
            'hostname' => 'catalog_search_solr_server_hostname',
            'port' => 'catalog_search_solr_server_port',
            'enableAuth' => 'catalog_search_solr_enable_auth',
            'index' => 'catalog_search_solr_index_prefix',
            'username' => 'catalog_search_solr_username',
            'password' => 'catalog_search_solr_password',
            'timeout' => 'catalog_search_solr_server_timeout',
        ];

        return array_merge(parent::_getFieldMapping(), $fields);
    }
}
