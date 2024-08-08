<?php

namespace Sh\Icanhazdadjoke\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Helper class for managing configuration settings related to the Icanhazdadjoke module.
 */
class Data extends AbstractHelper
{
    /**
     * Configuration writer instance
     *
     * @var WriterInterface
     */
    protected $configWriter;

    /**
     * Scope configuration instance
     *
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    public const CONFIG_PATH_API_DATA = 'icanhazdadjoke/general/api_data';
    public const CONFIG_PATH_ENABLE = 'icanhazdadjoke/general/enable';
    public const CONFIG_PATH_API_URL = 'icanhazdadjoke/general/api_url';
    public const CONFIG_PATH_API_MAX_PAGES = 'icanhazdadjoke/general/max_pages';

    /**
     * Constructor
     *
     * @param Context $context The context object
     * @param WriterInterface $configWriter The configuration writer interface
     * @param ScopeConfigInterface $scopeConfig The scope configuration interface
     */
    public function __construct(
        Context $context,
        WriterInterface $configWriter,
        ScopeConfigInterface $scopeConfig
    ) {
        parent::__construct($context);
        $this->configWriter = $configWriter;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Save a configuration value
     *
     * @param string $value The value to save
     * @param string $path The configuration path to save the value
     * @return void
     */
    public function saveApiData(string $value, string $path = self::CONFIG_PATH_API_DATA, string $scopeType = \Magento\Store\Model\ScopeInterface::SCOPE_DEFAULT): void
    {
        $this->configWriter->save($path, $value, $scopeType);
    }

    /**
     * Get a list of jokes from the configuration
     *
     * @param string $scopeType The scope type, default is STORE
     * @return array The list of jokes or an empty array if not set
     */
    public function getJokeList(string $scopeType = \Magento\Store\Model\ScopeInterface::SCOPE_STORE): array
    {
        $data = $this->getConfigValue(self::CONFIG_PATH_API_DATA);
        return json_decode($data, true) ?: [];
    }

    /**
     * Get a configuration value
     *
     * @param string $path The configuration path to retrieve the value from
     * @param string $scopeType The scope type, default is STORE
     * @return string|null The configuration value or null if not set
     */
    public function getConfigValue(string $path, string $scopeType = \Magento\Store\Model\ScopeInterface::SCOPE_STORE): ?string
    {
        return $this->scopeConfig->getValue($path, $scopeType);
    }

    /**
     * Check if the module is enabled
     *
     * @param string $scopeType The scope type, default is STORE
     * @return bool True if the module is enabled, false otherwise
     */
    public function isModuleEnabled(string $scopeType = \Magento\Store\Model\ScopeInterface::SCOPE_STORE): bool
    {
        $isEnabled = $this->getConfigValue(self::CONFIG_PATH_ENABLE, $scopeType);
        return $isEnabled === '1';
    }
}
