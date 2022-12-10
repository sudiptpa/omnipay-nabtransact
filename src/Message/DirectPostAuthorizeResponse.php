<?php

namespace Omnipay\NABTransact\Message;

use Omnipay\Common\Exception\RuntimeException;
use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RedirectResponseInterface;
use Omnipay\Common\Message\RequestInterface;
use Symfony\Component\HttpFoundation\RedirectResponse as HttpRedirectResponse;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

/**
 * NABTransact Direct Post Authorize Response.
 */
class DirectPostAuthorizeResponse extends AbstractResponse implements RedirectResponseInterface
{
    protected $redirectUrl;

    /**
     * @param RequestInterface $request
     * @param array            $data
     * @param $redirectUrl
     */
    public function __construct(RequestInterface $request, $data, $redirectUrl)
    {
        $this->request = $request;
        $this->data = $data;
        $this->redirectUrl = $redirectUrl;
    }

    public function isSuccessful()
    {
        return false;
    }

    public function isRedirect()
    {
        return true;
    }

    public function getRedirectUrl()
    {
        return $this->redirectUrl;
    }

    public function getRedirectMethod()
    {
        return 'POST';
    }

    public function getRedirectData()
    {
        return $this->getData();
    }

    /**
     * @return HttpRedirectResponse
     */
    public function getRedirectResponse()
    {
        if (!$this instanceof RedirectResponseInterface || !$this->isRedirect()) {
            throw new RuntimeException('This response does not support redirection.');
        }

        if ('GET' === $this->getRedirectMethod()) {
            return HttpRedirectResponse::create($this->getRedirectUrl());
        } elseif ('POST' === $this->getRedirectMethod()) {
            $hiddenFields = '';
            foreach ($this->getRedirectData() as $key => $value) {
                $hiddenFields .= sprintf(
                    '<input type="hidden" name="%1$s" value="%2$s" />',
                    htmlentities($key, ENT_QUOTES, 'UTF-8', false),
                    htmlentities($value, ENT_QUOTES, 'UTF-8', false)
                )."\n";
            }

            $style = '<style>
                * {
                    box-sizing: border-box;
                }

                body {
                    font-family: Arial, Helvetica, sans-serif;
                }

                .container {
                    width: 100%;
                    margin-top: 20%;
                    text-align: center;
                    display: table;
                }

                .loader {
                    border: 5px solid #f3f3f3;
                    border-radius: 50%;
                    border-top: 5px solid #3498db;
                    width: 50px;
                    height: 50px;
                    display: inline-block;
                    -webkit-animation: spin 2s linear infinite;
                    animation: spin 2s linear infinite;
                }

                @-webkit-keyframes spin {
                    0% { -webkit-transform: rotate(0deg); }
                    100% { -webkit-transform: rotate(360deg); }
                }

                @keyframes spin {
                    0% { transform: rotate(0deg); }
                    100% { transform: rotate(360deg); }
                }
            </style>';

            $output = '<!DOCTYPE html>
                        <html>
                            <head>
                                <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
                                <title>Redirecting...</title>
                                HEAD_STYLE
                            </head>
                            <body onload="document.forms[0].submit();">
                                <div class="container">
                                    <form action="%1$s" method="post">
                                        <p>Redirecting to payment page...</p>
                                        <div>
                                            %2$s
                                        </div>
                                    </form>
                                    <div class="loader"></div>
                                    <h4>Processing your payment ...</h4>
                                </div>
                            </body>
                        </html>';

            $output = vsprintf($output, [
                htmlentities($this->getRedirectUrl(), ENT_QUOTES, 'UTF-8', false),
                $hiddenFields,
            ]);

            $output = str_replace('HEAD_STYLE', $style, $output);

            return HttpResponse::create($output);
        }

        throw new RuntimeException('Invalid redirect method "'.$this->getRedirectMethod().'".');
    }
}
