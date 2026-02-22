# NAB Transact Feature Matrix

This matrix documents payment API feature coverage in this package.

## Scope

In scope:
- NAB Transact payment-processing APIs used in Omnipay flows.

Out of scope:
- NAB admin/reporting portal operations.
- Merchant account management workflows.

## SecureXML

| Feature | Omnipay Method | Class | Status |
| --- | --- | --- | --- |
| Echo/Test | `echoTest()` | `SecureXMLEchoTestRequest` | Implemented |
| Purchase | `purchase()` | `SecureXMLPurchaseRequest` | Implemented |
| Risk-managed purchase | `purchase()` with `riskManagement=true` | `SecureXMLRiskPurchaseRequest` | Implemented |
| Authorize | `authorize()` | `SecureXMLAuthorizeRequest` | Implemented |
| Capture | `capture()` | `SecureXMLCaptureRequest` | Implemented |
| Refund | `refund()` | `SecureXMLRefundRequest` | Implemented |
| XML response mapping | n/a | `SecureXMLResponse` | Implemented |

## DirectPost v2

| Feature | Omnipay Method | Class | Status |
| --- | --- | --- | --- |
| Purchase redirect | `purchase()` | `DirectPostPurchaseRequest` | Implemented |
| Authorize redirect | `authorize()` | `DirectPostAuthorizeRequest` | Implemented |
| Store-only redirect | `store()` | `DirectPostStoreRequest` | Implemented |
| Complete purchase callback | `completePurchase()` | `DirectPostCompletePurchaseRequest` | Implemented |
| Complete authorize callback | `completeAuthorize()` | `DirectPostCompletePurchaseRequest` | Implemented |
| Complete preauth (capture) | `capture()` | `DirectPostCaptureRequest` | Implemented |
| Refund (server-to-server) | `refund()` | `DirectPostRefundRequest` | Implemented |
| Reversal/void (server-to-server) | `void()` | `DirectPostReversalRequest` | Implemented |
| DirectPost API response mapper | n/a | `DirectPostApiResponse` | Implemented |
| Fingerprint verification helper | `webhook()` | `DirectPostWebhookRequest` | Implemented |
| EMV 3DS txnType mapping | `setHasEMV3DSEnabled(true)` | `DirectPostAbstractRequest` | Implemented |
| Risk-managed txnType mapping | `setHasRiskManagementEnabled(true)` | `DirectPostAbstractRequest` | Implemented |
| Risk + EMV txnType mapping | both flags enabled | `DirectPostAbstractRequest` | Implemented |
| EMV 3DS order creation API | `createEMV3DSOrder()` | `EMV3DSOrderRequest` | Implemented |

## Hosted Payment

| Feature | Omnipay Method | Class | Status |
| --- | --- | --- | --- |
| Hosted purchase redirect | `purchase()` | `HostedPaymentPurchaseRequest` | Implemented |
| Hosted callback completion | `completePurchase()` | `HostedPaymentCompletePurchaseRequest` | Implemented |
| Hosted callback response mapping | n/a | `HostedPaymentCompletePurchaseResponse` | Implemented |

## UnionPay

| Feature | Omnipay Method | Class | Status |
| --- | --- | --- | --- |
| UnionPay purchase redirect | `purchase()` | `UnionPayPurchaseRequest` | Implemented |
| UnionPay callback completion | `completePurchase()` | `UnionPayCompletePurchaseRequest` | Implemented |
| UnionPay completion response mapping | n/a | `UnionPayCompletePurchaseResponse` | Implemented |

## Transport Layer

| Feature | Class | Status |
| --- | --- | --- |
| Omnipay HTTP client adapter (default path) | `OmnipayHttpClientTransport` | Implemented |
| Native cURL transport | `CurlTransport` | Implemented |
| Custom transport contract | `TransportInterface` | Implemented |

## Notes

- Existing Omnipay request/response APIs are preserved for backward compatibility.
- Additional methods were added additively (no removal of existing methods).
