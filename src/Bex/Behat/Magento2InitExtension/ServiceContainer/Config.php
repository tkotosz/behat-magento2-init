<?php

namespace Bex\Behat\Magento2InitExtension\ServiceContainer;

class Config
{
    const CONFIG_KEY_MAGENTO_BOOTSTRAP_PATH = 'magento_bootstrap_path';
    const CONFIG_KEY_MAGENTO_CONFIGS = 'magento_configs';

    /**
     * @var string
     */
    private $magentoBootstrapPath;

    /**
     * @var array
     */
    private $magentoConfigs;

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->magentoBootstrapPath = $config[self::CONFIG_KEY_MAGENTO_BOOTSTRAP_PATH];
        $this->magentoConfigs = $config[self::CONFIG_KEY_MAGENTO_CONFIGS];
    }

    /**
     * @return string
     */
    public function getMagentoBootstrapPath()
    {
        return $this->magentoBootstrapPath;
    }

    /**
     * @return array
     */
    public function getRequiredMagentoConfig()
    {
        return $this->magentoConfigs;
    }
}
