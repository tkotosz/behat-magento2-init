<?php

namespace Bex\Behat\Magento2InitExtension\ServiceContainer;

class Config
{
    const CONFIG_KEY_MAGENTO_CONFIGS = 'magento_configs';

    /**
     * @var array
     */
    private $magentoConfigs;

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->magentoConfigs = $config[self::CONFIG_KEY_MAGENTO_CONFIGS];
    }

    /**
     * @return array
     */
    public function getRequiredMagentoConfig()
    {
        return $this->magentoConfigs;
    }
}
