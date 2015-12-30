<?php

namespace Bex\Behat\Magento2InitExtension\Listener;

use Behat\Testwork\EventDispatcher\Event\SuiteTested;
use Bex\Behat\Magento2InitExtension\Fixtures\MagentoConfigManager;
use Bex\Behat\Magento2InitExtension\ServiceContainer\Config;
use Magento\Framework\App\Bootstrap;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ObjectManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class Magento2InitListener implements EventSubscriberInterface
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var MagentoConfigManager
     */
    private $magentoConfigManager;

    /**
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            SuiteTested::BEFORE => 'initMagento',
            SuiteTested::AFTER => 'resetMagentoConfig'
        ];
    }

    public function initMagento()
    {
        $bootstrapPath = getcwd() . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'bootstrap.php';

        if (!file_exists($bootstrapPath)) {
            throw new \RuntimeException(sprintf("Magento's bootstrap file was not found at path '%s'", $bootstrapPath));
        }

        include $bootstrapPath;

        $params = $_SERVER;

        $params[Bootstrap::INIT_PARAM_FILESYSTEM_DIR_PATHS] = [
            DirectoryList::PUB => [DirectoryList::URL_PATH => ''],
            DirectoryList::MEDIA => [DirectoryList::URL_PATH => 'media'],
            DirectoryList::STATIC_VIEW => [DirectoryList::URL_PATH => 'static'],
            DirectoryList::UPLOAD => [DirectoryList::URL_PATH => 'media/upload'],
        ];

        putenv('BEHAT_RUNNING=true');

        $bootstrap = Bootstrap::create(BP, $params);
        $bootstrap->createApplication('Magento\Framework\App\Http');
        ObjectManager::getInstance();

        $this->magentoConfigManager = new MagentoConfigManager();
        $this->magentoConfigManager->changeConfigs($this->config->getRequiredMagentoConfig());
    }

    public function resetMagentoConfig()
    {
        $this->magentoConfigManager->revertAllConfig();
    }
}
