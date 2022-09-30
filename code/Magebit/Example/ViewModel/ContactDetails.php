<?php
namespace Magebit\Example\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

class ContactDetails implements ArgumentInterface
{

    private ScopeConfigInterface $scopeConfig;

    public function __construct(ScopeConfigInterface $scopeConfig )
    {
        $this->scopeConfig =$scopeConfig;
        
    }

    public function getPhoneNumber():String
    {
        return (string)$this->scopeConfig->getValue('general/store_information/phone');
    }
}
