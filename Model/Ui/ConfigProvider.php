<?php

namespace GoCryptoPay\GoCryptoPay\Model\Ui;
require __DIR__ . '/../../vendor/autoload.php';

use Eligmaltd\GoCryptoPayPHP\GoCryptoPay;
use Magento\Checkout\Model\ConfigProviderInterface;
use GoCryptoPay\GoCryptoPay\Gateway\Http\Client\ClientMock;

final class ConfigProvider implements ConfigProviderInterface
{
    const CODE = 'gocrypto_pay';

    public $scopeConfig;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    )
    {
        $this->scopeConfig = $scopeConfig;
    }

    public function getConfig()
    {
        $host = $this->scopeConfig->getValue('payment/gocrypto_pay/host', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $isSand = $this->scopeConfig->getValue('payment/gocrypto_pay/is_sandbox', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $gocrypto_pay = new GoCryptoPay($isSand);
        $config = $gocrypto_pay->config($host);

        return [
            'payment' => [
                self::CODE => [
                    'transactionResults' => [
                        ClientMock::SUCCESS => __('Success'),
                        ClientMock::FAILURE => __('Fraud')
                    ],
                    'prepareConfig' => $config
                ]
            ]
        ];
    }
}
