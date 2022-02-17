<?php

namespace Howard\PriceRange\Controller\Search;

class Search extends \Magento\Framework\App\Action\Action
{

  public function __construct(
    \Magento\Framework\App\Action\Context $context,
    \Magento\Framework\Controller\ResultFactory $resultFactory,
    \Magento\Framework\Controller\Result\JsonFactory  $resultJsonFactory,
    \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
    \Magento\Store\Model\StoreManagerInterface $storeManagerInterface,
     array $data = []
  ) {
    $this->resultFactory = $resultFactory;
    $this->resultJsonFactory = $resultJsonFactory;
    $this->productCollectionFactory = $productCollectionFactory;
    $this->storeManagerInterface = $storeManagerInterface;
    parent::__construct(
      $context,
      $data
    );
  }


  /**
   * Get product collection filter
   *
   * @param boolean $toArray
   * @return Magento\Catalog\Model\ResourceModel\Product\Collection|array
   */
  public function execute($toArray = true)
  {
    // Getting Form Submission Data
    $min_price = (int)$this->getRequest()->getParam('min-price');
    $max_price = (int)$this->getRequest()->getParam('max-price');
    $sort_by = $this->getRequest()->getParam('sort-by');
    // End Getting Form Submission Data


    // Validation Process
    if (!$min_price || !$max_price) {
      return false;
    };
    if ($max_price < $min_price) {
      return false;
    }
    if ($max_price > $min_price * 5) {
      return false;
    }
    if ($sort_by) {
      if($sort_by == "asc" || $sort_by == "desc"){
        switch($sort_by){
          case "asc":
            $sort = "ASC";
            break;
          case "desc":
            $sort = "DESC";
            break;
        }
      }else{
        return false;
      }
    }else{
      return false;
    }
    // End Validation


    // Begin Product Collection Filter
    $collection = $this->productCollectionFactory->create();
    $collection = $collection->addFieldToFilter('price', [
      'from' => $min_price,
      'to' => $max_price
    ])
    ->addAttributeToSelect('thumbnail')
    ->addAttributeToSelect('name')
    ->addAttributeToSelect('price')
    ->setOrder('price', $sort)
    ->joinField(
      'qty',
      'cataloginventory_stock_item',
      'qty',
      'product_id=entity_id'
    )
    ->setPageSize(10)
    ->setCurPage(1);
    $this->collection = $collection;
    // End Product Collection Filter

    // Convert Data into array Json and return to frontend
    if ($toArray) {
      return $this->productCollectionToArray();
    } else {
      return $this->collection;
    }

  }

  /**
   * Extract collection and return Json
   *
   * @return array
   */
  protected function productCollectionToArray()
  {
    $resultJason = $this->resultJsonFactory->create();

    $result = [];
    foreach ($this->collection as $product) {
        $productData = [
          'thumbnail' => $this->storeManagerInterface
          ->getStore()
          ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) .
          "catalog/product". $product->getThumbnail(),
          'sku' => $product->getSku(),
          'name' => $product->getName(),
          'qty' => $product->getQty(),
          'price' => $product->getFinalPrice(),
          'url' => $product->getProductUrl()
        ];
        $result[] = $productData;
    }

    return $resultJason->setData($result);
  }
}
