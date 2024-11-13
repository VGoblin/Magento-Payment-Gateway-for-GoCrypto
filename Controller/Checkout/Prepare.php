<?php

namespace GoCryptoPay\GoCryptoPay\Controller\Checkout;

require __DIR__ . '/../../vendor/autoload.php';

use Eligmaltd\GoCryptoPayPHP\GoCryptoPay;
use Magento\Framework\Controller\ResultFactory;
/**
 * Front Controller for Checkout Method
 * it does a redirect to checkout
 * Class Index
 * @package BeGateway\BeGateway\Controller\Checkout
 */
class Prepare extends \GoCryptoPay\GoCryptoPay\Controller\AbstractCheckoutAction
{

    public function execute()
    {
        $config = $this->getDataHelper()->getScopeConfig();
        $host = $config->getValue('payment/gocrypto_pay/host', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $isSand = $config->getValue('payment/gocrypto_pay/is_sandbox', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $gocryptoPay = new GoCryptoPay($isSand);
        $config = $gocryptoPay->config($host);
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData(['result' => $config]);
        return $resultJson;
    }
}
