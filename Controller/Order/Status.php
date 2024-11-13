<?php
namespace GoCryptoPay\GoCryptoPay\Controller\Order;

require __DIR__ . '/../../vendor/autoload.php';

use Eligmaltd\GoCryptoPayPHP\GoCryptoPay;
use Magento\Framework\Controller\ResultFactory;
use Magento\Sales\Api\OrderRepositoryInterface;


/**
 * Front Controller for Checkout Method
 * it does a redirect to checkout
 * Class Index
 * @package BeGateway\BeGateway\Controller\Checkout
 */
class Status extends \GoCryptoPay\GoCryptoPay\Controller\AbstractCheckoutAction
{

    public function execute()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        $transactionId = $this->getRequest()->getParam('transaction_id');
        $orderRepository = \Magento\Framework\App\ObjectManager::getInstance()->get(OrderRepositoryInterface::class);
        $order = $orderRepository->get($orderId);

        if ($order) {
            $config = $this->getDataHelper()->getScopeConfig();
            $host = $config->getValue('payment/gocrypto_pay/host', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $isSand = $config->getValue('payment/gocrypto_pay/is_sandbox', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $clientId = $config->getValue('payment/gocrypto_pay/client_id', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $clientSecret = $config->getValue('payment/gocrypto_pay/client_secret', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

            $gocryptoPay = new GoCryptoPay($isSand);
            $config = $gocryptoPay->config($host);

            $gocryptoPay->setCredentials($clientId, $clientSecret);
            if ($gocryptoPay->auth()) {
                if ($transactionId) {
                    $transactionStatus = $gocryptoPay->checkTransactionStatus($transactionId);
                    if ($transactionStatus == 'IN_PROGRESS') {
                        $this->_redirect('checkout/onepage/pending');
                        return;
                    } else if ($transactionStatus == 'SUCCESS') {

                        $order->setState('processing')
                            ->setStatus('processing')
                            ->addStatusHistoryComment(__('Payment approved for Transaction ID: "%1".', $transactionId));

                        $orderRepository->save($order);
                        $this->_redirect('checkout/onepage/success');
                        return;
                    } else {
                        $order->setState('canceled')
                            ->setStatus('canceled')
                            ->addStatusHistoryComment(__('Payment declined for Transaction ID: "%1".', $transactionId));

                        $orderRepository->save($order);
                        $this->_redirect('checkout/cart');
                        return;
                    }
                } else {
                    $order->setState('canceled')
                        ->setStatus('canceled')
                        ->addStatusHistoryComment(__('Payment declined for Transaction ID: "%1".', $transactionId));
                    $orderRepository->save($order);
                    $this->_redirect('checkout/cart');
                    return;
                }
            } else {
                $order->setState('canceled')
                    ->setStatus('canceled')
                    ->addStatusHistoryComment(__('Payment declined for Transaction ID: "%1".', $transactionId));
                $orderRepository->save($order);
                $this->_redirect('checkout/cart');
                return;
            }
        }
    }
}
