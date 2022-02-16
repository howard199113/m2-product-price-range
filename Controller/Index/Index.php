<?php

namespace Howard\PriceRange\Controller\Index;


class Index extends \Magento\Framework\App\Action\Action

{

    protected $_pageFactory;

    public function __construct(

       \Magento\Framework\App\Action\Context $context,

       \Magento\Framework\View\Result\PageFactory $pageFactory)

    {

       $this->_pageFactory = $pageFactory;

       return parent::__construct($context);

    }

    public function execute()

    {
      $resultPage = $this->_pageFactory->create();
      $resultPage->getConfig()->getTitle()->set('Product Price Range');
      return $resultPage;

    }

}
