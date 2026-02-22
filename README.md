# Omnipay: NAB Transact

**NAB Transact driver for the Omnipay PHP payment processing library**

[![CI](https://github.com/sudiptpa/omnipay-nabtransact/actions/workflows/ci.yml/badge.svg)](https://github.com/sudiptpa/omnipay-nabtransact/actions/workflows/ci.yml)
[Omnipay](https://github.com/thephpleague/omnipay) is a framework agnostic, multi-gateway payment
processing library for PHP. This package implements NAB Transact support for Omnipay.

[![Latest Stable Version](https://poser.pugx.org/sudiptpa/omnipay-nabtransact/v/stable?style=flat-square)](https://packagist.org/packages/sudiptpa/omnipay-nabtransact)
[![Total Downloads](https://poser.pugx.org/sudiptpa/omnipay-nabtransact/downloads?style=flat-square)](https://packagist.org/packages/sudiptpa/omnipay-nabtransact)
[![GitHub license](https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square)](https://raw.githubusercontent.com/sudiptpa/omnipay-nabtransact/master/LICENSE)

## Installation

Omnipay is installed via [Composer](http://getcomposer.org/). To install, simply require `league/omnipay` and `sudiptpa/omnipay-nabtransact` with Composer:

```
composer require league/omnipay sudiptpa/omnipay-nabtransact
```

## Basic Usage

The following gateways are provided by this package:

* NABTransact_DirectPost (NAB Transact Direct Post v2)
* NABTransact_SecureXML (NAB Transact SecurePay XML)
* NABTransact_HostedPayment (NAB Hosted Payment Page)
* NABTransact_UnionPay (UnionPay via NAB Transact)

### NAB Transact SecureXML API

```php
    use Omnipay\Omnipay;
    use Omnipay\Common\CreditCard;

    $gateway = Omnipay::create('NABTransact_SecureXML');
    $gateway->setMerchantId('XYZ0010');
    $gateway->setTransactionPassword('abcd1234');
    $gateway->setTestMode(true);

    $card = new CreditCard([
            'firstName' => 'Sujip',
            'lastName' => 'Thapa',
            'number'      => '4444333322221111',
            'expiryMonth' => '06',
            'expiryYear'  => '2030',
            'cvv'         => '123',
        ]
    );

    $transaction = $gateway->purchase([
            'amount'        => '10.00',
            'currency'      => 'AUD',
            'transactionId' => 'XYZ100',
            'card'          => $card,
        ]
    );

    $response = $transaction->send();

    if ($response->isSuccessful()) {
        echo sprintf('Transaction %s was successful!', $response->getTransactionReference());
    } else {
        echo sprintf('Transaction %s failed: %s', $response->getTransactionReference(), $response->getMessage());
    }

```


### NAB Transact SecureXML API with Risk Management

```php
    use Omnipay\Omnipay;
    use Omnipay\Common\CreditCard;

    $gateway = Omnipay::create('NABTransact_SecureXML');
    $gateway->setMerchantId('XYZ0010');
    $gateway->setTransactionPassword('abcd1234');
    $gateway->setTestMode(true);
    $gateway->setRiskManagement(true);

    $card = new CreditCard([
            'firstName' => 'Sujip',
            'lastName' => 'Thapa',
            'number'      => '4444333322221111',
            'expiryMonth' => '06',
            'expiryYear'  => '2030',
            'cvv'         => '123',
        ]
    );

    $transaction = $gateway->purchase([
            'amount'        => '10.00',
            'currency'      => 'AUD',
            'transactionId' => 'XYZ100',
            'card'          => $card,
            'ip'            => '1.1.1.1',
        ]
    );

    $response = $transaction->send();

    if ($response->isSuccessful()) {
        echo sprintf('Transaction %s was successful!', $response->getTransactionReference());
    } else {
        echo sprintf('Transaction %s failed: %s', $response->getTransactionReference(), $response->getMessage());
    }
```

### NAB Transact DirectPost v2

```php
    $gateway = Omnipay::create('NABTransact_DirectPost');

    $gateway->setMerchantId('XYZ0010');
    $gateway->setTransactionPassword('abcd1234');
    $gateway->setTestMode(true);
    $gateway->setHasEMV3DSEnabled(true);

    $card = new CreditCard(array(
        'firstName' => 'Sujip',
        'lastName' => 'Thapa',
        'number' => '4444333322221111',
        'expiryMonth' => '10',
        'expiryYear' => '2030',
        'cvv' => '123',
    ));

    $response = $gateway->purchase(array(
        'amount' => '12.00',
        'transactionId' => 'ORDER-ZYX8',
        'transactionReference' => '11fc42b0-bb7a-41a4-8b3c-096b3fd4d402',
        'currency' => 'AUD',
        'card' => $card,
        'clientIp' => '192.168.1.1'
    ))
        ->send();

    if ($response->isRedirect()) {
        $response->redirect();
    }

    if ($response->isSuccessful()) {
        echo sprintf('Transaction %s was successful!', $response->getTransactionReference());
    } else {
        echo sprintf('Transaction %s failed: %s', $response->getTransactionReference(), $response->getMessage());
    }

```

#### DirectPost Store Only

```php
    $gateway = Omnipay::create('NABTransact_DirectPost');
    $gateway->setMerchantId('XYZ0010');
    $gateway->setTransactionPassword('abcd1234');
    $gateway->setTestMode(true);

    $response = $gateway->store(array(
        'transactionId' => 'STORE-ORDER-100',
        'returnUrl' => 'http://example.com/payment/response',
        'card' => $card,
    ))->send();

    if ($response->isRedirect()) {
        $response->redirect();
    }
```

#### DirectPost Capture (Complete Preauth, Server-to-Server)

```php
    $gateway = Omnipay::create('NABTransact_DirectPost');
    $gateway->setMerchantId('XYZ0010');
    $gateway->setTransactionPassword('abcd1234');
    $gateway->setTestMode(true);

    $response = $gateway->capture(array(
        'transactionId' => 'CAPTURE-ORDER-100',
        'transactionReference' => 'NAB-ORIGINAL-TXN-ID',
        'amount' => '12.00',
        'currency' => 'AUD',
    ))->send();

    if ($response->isSuccessful()) {
        echo 'Capture successful: '.$response->getTransactionReference();
    }
```

#### DirectPost Refund (Server-to-Server)

```php
    $response = $gateway->refund(array(
        'transactionId' => 'REFUND-ORDER-100',
        'transactionReference' => 'NAB-SETTLED-TXN-ID',
        'amount' => '5.00',
        'currency' => 'AUD',
    ))->send();
```

#### DirectPost Reversal/Void (Server-to-Server)

```php
    $response = $gateway->void(array(
        'transactionId' => 'VOID-ORDER-100',
        'transactionReference' => 'NAB-AUTH-TXN-ID',
        'amount' => '12.00',
    ))->send();
```

#### EMV 3DS Order Creation API

```php
    $response = $gateway->createEMV3DSOrder(array(
        'amount' => '12.00',
        'currency' => 'AUD',
        'clientIp' => '203.0.113.10',
        'transactionReference' => 'ORDER-REF-100',
    ))->send();

    if ($response->isSuccessful()) {
        echo 'Order ID: '.$response->getOrderId();
        echo 'Simple Token: '.$response->getSimpleToken();
    }
```

### NAB Transact DirectPost v2 UnionPay Online Payment

```php
    $gateway = Omnipay::create('NABTransact_UnionPay');

    $gateway->setMerchantId('XYZ0010');
    $gateway->setTransactionPassword('abcd1234');

    $gateway->setTestMode(true);

    /*
     * The parameter transactionId must be alpha-numeric and 8 to 32 characters in length
     */

    $response = $gateway->purchase(array(
        'amount' => '12.00',
        'transactionId' => '1234566789205067',
        'currency' => 'AUD',
        'returnUrl' => 'http://example.com/payment/response',
    ))
        ->send();

    if ($response->isRedirect()) {
        $response->redirect();
    }
```

#### Complete Purchase

```php
    $gateway = Omnipay::create('NABTransact_UnionPay');

    $gateway->setMerchantId('XYZ0010');
    $gateway->setTransactionPassword('abcd1234');

    $gateway->setTestMode(true);

    $response = $gateway->completePurchase(array(
        'amount' => '12.00',
        'transactionId' => '1234566789205067',
        'transactionReference' => '11fc42b0-bb7a-41a4-8b3c-096b3fd4d402',
        'currency' => 'AUD',
        'returnUrl' => 'http://example.com/payment/response',
    ))
        ->send();

    if ($response->isSuccessful()) {
        echo sprintf('Transaction %s was successful!', $response->getTransactionReference());
    } else {
        echo sprintf('Transaction %s failed: %s', $response->getTransactionReference(), $response->getMessage());
    }

```

For general usage instructions, please see the main [Omnipay](https://github.com/thephpleague/omnipay)
repository.

## Framework-Agnostic Transport Option

Omnipay support remains the default behavior. SecureXML requests can now also use a framework-agnostic cURL transport:

```php
use Omnipay\NABTransact\Transport\CurlTransport;

$request = $gateway->purchase([
    'amount' => '10.00',
    'transactionId' => 'ORDER-1000',
    'card' => $card,
]);

$request->setTransport(new CurlTransport());
$response = $request->send();
```

DirectPost server-to-server operations (`capture`, `refund`, `void`, `createEMV3DSOrder`) can use the same transport injection pattern.

See `ARCHITECTURE.md` for design details and extension points.

## NAB Feature Coverage

Core payment features are mapped in `docs/feature-matrix.md` with request/response classes and test coverage.
This package targets payment-processing API coverage and does not include NAB admin/reporting portal features.

## Contributing

Contributions are **welcome** and will be fully **credited**.

Contributions can be made via a Pull Request on [Github](https://github.com/sudiptpa/omnipay-nabtransact).

## Support

If you are having general issues with Omnipay, we suggest posting on
[Stack Overflow](http://stackoverflow.com/). Be sure to add the
[omnipay tag](http://stackoverflow.com/questions/tagged/omnipay) so it can be easily found.

If you want to keep up to date with release anouncements, discuss ideas for the project,
or ask more detailed questions, there is also a [mailing list](https://groups.google.com/forum/#!forum/omnipay) which
you can subscribe to.

If you believe you have found a bug, please report it using the [GitHub issue tracker](https://github.com/sudiptpa/nabtransact/issues),
or better yet, fork the library and submit a pull request.


## Architecture

See `ARCHITECTURE.md` for package structure, flow, and extension points.
