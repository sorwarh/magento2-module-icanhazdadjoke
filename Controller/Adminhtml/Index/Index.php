<?php

namespace Sh\Icanhazdadjoke\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action
{
    protected $resultPageFactory;

    const ADMIN_RESOURCE = 'Sh_Icanhazdadjoke::listing';

    public function __construct(
        Action\Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Sh_Icanhazdadjoke::jokes');
        $resultPage->getConfig()->getTitle()->prepend(__('Jokes List'));
        return $resultPage;
    }
}
