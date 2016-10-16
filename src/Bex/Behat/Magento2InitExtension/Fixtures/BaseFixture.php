<?php

namespace Bex\Behat\Magento2InitExtension\Fixtures;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\State;
use Magento\Framework\ObjectManager\ConfigLoaderInterface;
use Magento\Framework\Registry;

abstract class BaseFixture
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * init object manager and set app state
     */
    public function __construct()
    {
        $this->initObjectManager();
        $this->initAppState();
    }

    /**
     * @return ObjectManager
     */
    private function initObjectManager()
    {
        if (is_null($this->objectManager)) {
            $this->objectManager = ObjectManager::getInstance();
        }
        
        return $this->objectManager;
    }

    /**
     * init app state (add adminhtml configs as well)
     */
    private function initAppState()
    {
        $appState = $this->getMagentoObject(State::class);
        $configLoader = $this->getMagentoObject(ConfigLoaderInterface::class);
        $registry = $this->getMagentoObject(Registry::class);
        
        try {
            $appState->getAreaCode();
        } catch (\Exception $e) {
            $appState->setAreaCode('adminhtml');
            $this->objectManager->configure($configLoader->load('adminhtml'));
            $registry->register('isSecureArea', true);
        }
    }

    /**
     * @param  string $type
     *
     * @return mixed
     */
    protected function getMagentoObject($type)
    {
        return $this->objectManager->get($type);
    }

    /**
     * @param  string $type
     * @param  array  $arguments
     *
     * @return mixed
     */
    protected function createMagentoObject($type, array $arguments = [])
    {
        return $this->objectManager->create($type, $arguments);
    }

    /**
     * @return ObjectManager
     */
    protected function getObjectManager()
    {
        return $this->objectManager;
    }
}
