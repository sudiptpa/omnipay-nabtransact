# Omnipay: NAB Transact

**NAB Transact driver for the Omnipay PHP payment processing library**

[Omnipay](https://github.com/thephpleague/omnipay) is a framework agnostic, multi-gateway payment
processing library for PHP 5.3+. This package implements NAB Transact support for Omnipay.

[![StyleCI](https://styleci.io/repos/74269379/shield?branch=master)](https://styleci.io/repos/74269379)
[![Build Status](https://travis-ci.org/sudiptpa/omnipay-nabtransact.svg?branch=master)](https://travis-ci.org/sudiptpa/omnipay-nabtransact)
[![Latest Stable Version](https://poser.pugx.org/sudiptpa/omnipay-nabtransact/v/stable)](https://packagist.org/packages/sudiptpa/omnipay-nabtransact)
[![Total Downloads](https://poser.pugx.org/sudiptpa/omnipay-nabtransact/downloads)](https://packagist.org/packages/sudiptpa/omnipay-nabtransact)
[![GitHub license](https://img.shields.io/badge/license-MIT-blue.svg)](https://raw.githubusercontent.com/sudiptpa/omnipay-nabtransact/master/LICENSE)

## Installation

Omnipay is installed via [Composer](http://getcomposer.org/). To install, simply add it
to your `composer.json` file:

```json
{
    "require": {
        "sudiptpa/omnipay-nabtransact": "~2.0"
    }
}
```

And run composer to update your dependencies:

    $ curl -s http://getcomposer.org/installer | php
    $ php composer.phar update

## Basic Usage

The following gateways are provided by this package:

* NAB Transact SecureXML API

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

For general usage instructions, please see the main [Omnipay](https://github.com/thephpleague/omnipay)
repository.

## Support

If you are having general issues with Omnipay, we suggest posting on
[Stack Overflow](http://stackoverflow.com/). Be sure to add the
[omnipay tag](http://stackoverflow.com/questions/tagged/omnipay) so it can be easily found.

If you want to keep up to date with release anouncements, discuss ideas for the project,
or ask more detailed questions, there is also a [mailing list](https://groups.google.com/forum/#!forum/omnipay) which
you can subscribe to.

If you believe you have found a bug, please report it using the [GitHub issue tracker](https://github.com/sudiptpa/nabtransact/issues),
or better yet, fork the library and submit a pull request.
