<?php

require __DIR__.'/vendor/autoload.php';

use Omnipay\Common\CreditCard;
use Omnipay\Omnipay;

$gateway = Omnipay::create('NABTransact_SecureXML');

$gateway->setMerchantId('XYZ0010');
$gateway->setTransactionPassword('abcd1234');

$gateway->setTestMode(true);

$card = new CreditCard(array(
    'firstName'   => 'Sujip',
    'lastName'    => 'Thapa',
    'number'      => '4444333322221111',
    'expiryMonth' => '10',
    'expiryYear'  => '2030',
    'cvv'         => '123',
));

$response = $gateway->purchase(array(
    'amount'        => '12.00',
    'transactionId' => 'ORDER-ZYX8',
    'currency'      => 'AUD',
    'card'          => $card,
))
    ->send();

$message = sprintf(
    'Transaction with reference code  (%s) - %s',
    $response->getTransactionReference(),
    $response->getMessage()
);

echo $message;
