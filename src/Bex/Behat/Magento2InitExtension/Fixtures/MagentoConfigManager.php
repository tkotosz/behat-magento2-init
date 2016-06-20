<?php

namespace Bex\Behat\Magento2InitExtension\Fixtures;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\CacheInterface;
use Magento\Store\Model\ScopeInterface;

class MagentoConfigManager extends BaseFixture
{
    /**
     * @var array
     */
    private $configAttributes = ['path', 'value', 'scope_type', 'scope_code'];

    /**
     * @var ScopeConfigInterface
     */
    private $configReader;

    /**
     * @var WriterInterface
     */
    private $configWriter;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var CacheInterface
     */
    private $cache;

    /**
     * @var array
     */
    private $originalConfigs = [];

    public function __construct()
    {
        parent::__construct();
        $this->configReader = $this->getMagentoObject(ScopeConfigInterface::class);
        $this->configWriter = $this->getMagentoObject(WriterInterface::class);
        $this->storeManager = $this->getMagentoObject(StoreManagerInterface::class);
        $this->cache = $this->getMagentoObject(CacheInterface::class);
        $this->originalConfigs = [];
    }

    /**
     * @param  array  $configs
     *
     * @return void
     */
    public function changeConfigs(array $configs)
    {
        foreach ($configs as $config) {
            
            if (!array_key_exists('scope_type', $config)) {
                $config['scope_type'] = ScopeConfigInterface::SCOPE_TYPE_DEFAULT;
            }

            if (!array_key_exists('scope_code', $config)) {
                $config['scope_code'] = null;
            }            
            
            if ($this->isValidConfig($config)) {
                $this->changeConfig($config['path'], $config['value'], $config['scope_type'], $config['scope_code']);
            }
        }

        $this->cache->clean();
    }

    /**
     * Revert the original configs
     */
    public function revertAllConfig()
    {
        $this->changeConfigs($this->originalConfigs);
        $this->originalConfigs = [];
    }

    /**
     * @param  array  $config
     *
     * @return boolean
     */
    private function isValidConfig(array $config)
    {
        foreach ($this->configAttributes as $attribute) {
            if (!array_key_exists($attribute, $config)) {
                return false;
            }
        }

        return !empty($config['path']);
    }

    /**
     * @param  string $path
     * @param  mixed  $value
     * @param  string $scopeType
     * @param  string $scopeCode
     */
    private function changeConfig($path, $value, $scopeType, $scopeCode)
    {
        $originalValue = $this->configReader->getValue($path, $scopeType, $scopeCode);
        $this->storeOrigConfig($path, $originalValue, $scopeType, $scopeCode);
        $this->configWriter->save($path, $value, $scopeType, $this->getScopeIdByScopeCode($scopeType, $scopeCode));
    }

    /**
     * @param  string $path
     * @param  string $value
     * @param  string $scopeType
     * @param  string $scopeCode
     */
    private function storeOrigConfig($path, $value, $scopeType, $scopeCode)
    {
        $this->originalConfigs[] = [
            'path' => $path,
            'value' => $value,
            'scope_type' => $scopeType,
            'scope_code' => $scopeCode
        ];
    }

    /**
     * @param  string $scopeType
     * @param  string $scopeCode
     *
     * @return array
     */
    private function getScopeIdByScopeCode($scopeType, $scopeCode)
    {
        $scopeId = 0;

        $scopesByCode = $this->getScopesByCode($scopeType);

        if (isset($scopesByCode[$scopeCode])) {
            $scope = $scopesByCode[$scopeCode];
            $scopeId = $scope->getId();
        }

        return $scopeId;
    }

    /**
     * @param  string $scopeType
     *
     * @return array
     */
    private function getScopesByCode($scopeType)
    {
        $scopesByType = [];

        if ($scopeType == ScopeInterface::SCOPE_STORES) {
            $scopesByType = $this->storeManager->getStores(false, true);
        }

        if ($scopeType == ScopeInterface::SCOPE_WEBSITES) {
            $scopesByType = $this->storeManager->getWebsites(false, true);
        }

        return $scopesByType;
    }
}
