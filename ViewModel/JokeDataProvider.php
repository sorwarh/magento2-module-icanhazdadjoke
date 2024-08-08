<?php

namespace Sh\Icanhazdadjoke\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Sh\Icanhazdadjoke\Helper\Data;

class JokeDataProvider implements ArgumentInterface
{
    private Data $dataHelper;

    /**
     * Constructor
     *
     * @param Data $dataHelper
     */
    public function __construct(Data $dataHelper)
    {
        $this->dataHelper = $dataHelper;
    }

    /**
     * Get jokes data from the configuration
     *
     * @return array
     */
    public function getJokesData(): array
    {

        $data = $this->dataHelper->getJokeList();
        return $data['results'] ?? [];
    }

    /**
     * Check if the module is enabled in the configuration
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        return (bool) $this->dataHelper->isModuleEnabled();
    }
}
