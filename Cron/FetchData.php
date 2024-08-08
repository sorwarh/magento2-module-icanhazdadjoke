<?php

namespace Sh\Icanhazdadjoke\Cron;

use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\HTTP\Client\Curl;
use Psr\Log\LoggerInterface;

class FetchData
{
    /**
     * @var Curl
     */
    protected $curl;

    /**
     * @var WriterInterface
     */
    protected $configWriter;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Configuration path for API data
     */
    private const CONFIG_PATH_API_DATA = 'icanhazdadjoke/general/api_data';

    /**
     * Constructor
     *
     * @param Curl $curl
     * @param WriterInterface $configWriter
     * @param LoggerInterface $logger
     */
    public function __construct(
        Curl $curl,
        WriterInterface $configWriter,
        LoggerInterface $logger
    ) {
        $this->curl = $curl;
        $this->configWriter = $configWriter;
        $this->logger = $logger;
    }

    /**
     * Execute the cron job
     *
     * @return void
     */
    public function execute(): void
    {
        try {
            // @TODO use HelperClass
            $url = 'https://icanhazdadjoke.com/search?page=' . rand(0, 38);
            $this->curl->addHeader('Accept', 'application/json');
            $this->curl->get($url);
            $data = json_decode($this->curl->getBody(), true);

            // Log the URL and response for debugging
            $this->logger->info('FetchData - URL: ' . $url);
            $this->logger->info('FetchData - Response: ' . $this->curl->getBody());

            if ($data && is_array($data)) {
                $this->configWriter->save(self::CONFIG_PATH_API_DATA, json_encode($data), 'default');

                $jokeCount = isset($data['results']) ? count($data['results']) : 0;
                $this->logger->info('Sh_Icanhazdadjoke - Data fetched successfully. Total jokes: ' . $jokeCount);
            } else {
                $this->logger->info('Sh_Icanhazdadjoke - No data fetched or data format is incorrect.');
            }
        } catch (\Exception $e) {
            $this->logger->error('Sh_Icanhazdadjoke - Error: ' . $e->getMessage());
        }
    }
}
