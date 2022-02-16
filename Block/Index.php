<?php
namespace Howard\PriceRange\Block;
class Index extends \Magento\Framework\View\Element\Template
{
  /**
 * Get form submission URL
 *
 * @return string
 */

 public function getSubmitUrl()
 {
   return $this->getUrl('pricerange/search/search');
 }
}
