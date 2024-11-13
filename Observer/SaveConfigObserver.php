<?php
namespace GoCryptoPay\GoCryptoPay\Observer;

require __DIR__ . '/../vendor/autoload.php';

use Eligmaltd\GoCryptoPayPHP\GoCryptoPay;
use Magento\Framework\Event\Observer;
use Magento\Payment\Observer\AbstractDataAssignObserver;
use Psr\Log\LoggerInterface as Logger;

class SaveConfigObserver extends AbstractDataAssignObserver
{

    /**
     * @var Logger
     */
    protected $logger;
    protected $_resourceConfig;
    protected $_appConfig;

    public $scopeConfig;


    /**
     * @param Logger $logger
     */
    public function __construct(
        Logger $logger,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Config\Model\ResourceModel\Config $resourceConfig,
        \Magento\Framework\App\Config\ReinitableConfigInterface $config
    ) {
        $this->logger = $logger;
        $this->scopeConfig = $scopeConfig;
        $this->_resourceConfig = $resourceConfig;
        $this->_appConfig = $config;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {

        $this->logger->info('===============================================================================');

        if( !$this->scopeConfig->getValue('payment/gocrypto_pay/client_id', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)){
            $host = $this->scopeConfig->getValue('payment/gocrypto_pay/host', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $isSand = $this->scopeConfig->getValue('payment/gocrypto_pay/is_sandbox', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $otp = $this->scopeConfig->getValue('payment/gocrypto_pay/otp', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $terminalID = $this->scopeConfig->getValue('payment/gocrypto_pay/terminal_id', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

            $gocryptoPay = new GoCryptoPay($isSand);
            $config = $gocryptoPay->config($host);

            if (!is_string($config)) {
                $pairResponse = $gocryptoPay->pair($terminalID, $otp);
                if (!is_string($pairResponse)) {
                    $clientId = $pairResponse['client_id'];
                    $clientSecret = $pairResponse['client_secret'];

                    $this->_resourceConfig->saveConfig('payment/gocrypto_pay/client_id', $clientId);
                    $this->_resourceConfig->saveConfig('payment/gocrypto_pay/client_secret', $clientSecret);
                }
            }
            $this->_appConfig->reinit();
        }

        $this->logger->info('===============================================================================');
    }
}
