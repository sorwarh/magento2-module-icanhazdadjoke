<?php
namespace Sh\Icanhazdadjoke\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Cache\Frontend\Pool;
use Sh\Icanhazdadjoke\Helper\Data as DataHelper;

/**
 * Class Fetch
 * 
 * Controller class responsible for fetching jokes from 
 * the ICanHazDadJoke API and saving the data to the configuration.
 */
class Fetch extends Action
{
    /**
     * @var RedirectFactory
     */
    protected $resultRedirectFactory;

    /**
     * @var WriterInterface
     */
    protected $configWriter;

    /**
     * @var Curl
     */
    protected $curl;

    /**
     * @var TypeListInterface
     */
    protected $cacheTypeList;

    /**
     * @var Pool
     */
    protected $cacheFrontendPool;

    /**
     * @var DataHelper
     */
    protected $helper;

    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Sh_Icanhazdadjoke::listing';


    /**
     * Summary of __construct
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\RedirectFactory $resultRedirectFactory
     * @param \Magento\Framework\App\Config\Storage\WriterInterface $configWriter
     * @param \Magento\Framework\HTTP\Client\Curl $curl
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool
     * @param \Sh\Icanhazdadjoke\Helper\Data $dataHelper
     */
    public function __construct(
        Action\Context $context,
        RedirectFactory $resultRedirectFactory,
        WriterInterface $configWriter,
        Curl $curl,
        TypeListInterface $cacheTypeList,
        Pool $cacheFrontendPool,
        DataHelper $dataHelper

    ) {
        parent::__construct($context);
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->configWriter = $configWriter;
        $this->curl = $curl;
        $this->cacheTypeList = $cacheTypeList;
        $this->cacheFrontendPool = $cacheFrontendPool;
        $this->helper = $dataHelper;
    }

    /**
     * 
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        try {
            // Get the API URL from the helper data class
            $apiUrl = $this->helper->getConfigValue(DataHelper::CONFIG_PATH_API_URL);

            if (!$apiUrl) {
                throw new \Exception('Sh_Icanhazdadjoke -- API URL is not configured.');
            }

            // Fetch data from the API
            $maxPages = (int) DataHelper::CONFIG_PATH_API_MAX_PAGES;
            $url = $apiUrl . 'search?page=' . rand(0, $maxPages);
            $this->curl->addHeader('Accept', 'application/json');
            $this->curl->get($url);
            $data = json_decode($this->curl->getBody(), true);

            if ($data && is_array($data)) {
                $this->configWriter->save(DataHelper::CONFIG_PATH_API_DATA, json_encode($data));
                $this->messageManager->addSuccessMessage(__('Jokes list updated successfully.'));
            } else {
                $this->messageManager->addErrorMessage(__('Failed to fetch jokes.'));
            }
            $this->invalidateCache();

        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Sh_Icanhazdadjoke -- An error occurred while fetching the jokes.'));
        }

        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('*/*/index');
    }


    /**
     * @return void
     */
    protected function invalidateCache()
    {
        $this->cacheTypeList->cleanType('config');
    }
}
