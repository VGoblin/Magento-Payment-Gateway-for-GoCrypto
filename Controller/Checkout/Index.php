<?php
/*
 * Copyright (C) 2017 beGateway
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * @author      beGateway
 * @copyright   2017 beGateway
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2 (GPL-2.0)
 */

namespace GoCryptoPay\GoCryptoPay\Controller\Checkout;

require __DIR__ . '/../../vendor/autoload.php';

use Eligmaltd\GoCryptoPayPHP\GoCryptoPay;
use GoCryptoPay\GoCryptoPay\Helper\Data;
use Magento\Framework\Controller\ResultFactory;
use Magento\Sales\Api\OrderRepositoryInterface;

/**
 * Front Controller for Checkout Method
 * it does a redirect to checkout
 * Class Index
 * @package BeGateway\BeGateway\Controller\Checkout
 */
class Index extends \GoCryptoPay\GoCryptoPay\Controller\AbstractCheckoutAction
{

    /**
     * Redirect to checkout
     *
     * @return void
     */

    public function __construct(\Magento\Framework\App\Action\Context $context, \Psr\Log\LoggerInterface $logger,
                                \Magento\Checkout\Model\Session $checkoutSession, \Magento\Sales\Model\OrderFactory $orderFactory,
                                Data $dataHelper, \Magento\Store\Model\StoreManagerInterface $storeManager)
    {
        $this->_storeManager = $storeManager;
        parent::__construct($context, $logger, $checkoutSession, $orderFactory, $dataHelper);
    }
    public function execute()
    {
        $order = $this->getOrder();
        $baseUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB);
        if (!isset($order)) return;

        $config = $this->getDataHelper()->getScopeConfig();
        $host = $config->getValue('payment/gocrypto_pay/host', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $isSand = $config->getValue('payment/gocrypto_pay/is_sandbox', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $clientId = $config->getValue('payment/gocrypto_pay/client_id', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $clientSecret = $config->getValue('payment/gocrypto_pay/client_secret', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        $orderId = $order->getId();

        // set charge data
        $chargeData = array(
            'shop_name' => $host,
            'shop_description' => $host,
            'language' => 'en',
            'order_number' => $orderId,
            'amount' => round($order->getTotalDue() * 100),
            'discount' => round($order->getDiscountAmount() * 100),
            'currency_code' => $order->getBaseCurrencyCode(),
            'customer_email' => $order->getBillingAddress()->getEmail(),
            'callback_endpoint' => $baseUrl.'gocryptopay/order/status?order_id='.$orderId
        );

        foreach ($order->getAllItems() as $itemID => $item) {
            $itemData = [
                'name' => $item->getName(),
                'quantity' => $item->getQtyOrdered(),
                'price' => round($item->getRowTotal() * 100),
                'tax' => round($item->getTaxAmount() * 100)
            ];

            $chargeData['items'][] = $itemData;
        }

        $gocryptoPay = new GoCryptoPay($isSand);
        $config = $gocryptoPay->config($host);

        try {
            if (!is_string($config)) {
                $gocryptoPay->setCredentials($clientId, $clientSecret);
                if ($gocryptoPay->auth()) {

                    $charge = $gocryptoPay->generateCharge($chargeData);
                    $redirectUrl = $charge['redirect_url'];

                    $this->getResponse()->setRedirect($redirectUrl);
                } else {
                    echo 'auth-failed';
                    exit();
                }
            } else {
                echo 'config failed: '. $config;
                exit();
            }
        } catch (Exception $e) {
            printf($e, 1);
            exit();
        }
    }
}
