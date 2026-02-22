# NAB Transact Implemented Features Tree

This tree is generated from the current package code (gateway methods + request/response classes).

```text
omnipay-nabtransact
├── Gateways
│   ├── NABTransact_SecureXML (SecureXMLGateway)
│   │   ├── echoTest() -> SecureXMLEchoTestRequest
│   │   ├── authorize() -> SecureXMLAuthorizeRequest
│   │   ├── purchase() -> SecureXMLPurchaseRequest
│   │   ├── purchase() + riskManagement=true -> SecureXMLRiskPurchaseRequest
│   │   ├── capture() -> SecureXMLCaptureRequest
│   │   ├── refund() -> SecureXMLRefundRequest
│   │   ├── transport override (setTransport/getTransport)
│   │   └── timeout control (setTimeoutSeconds/getTimeoutSeconds)
│   │
│   ├── NABTransact_DirectPost (DirectPostGateway)
│   │   ├── Redirect flows
│   │   │   ├── authorize() -> DirectPostAuthorizeRequest
│   │   │   ├── purchase() -> DirectPostPurchaseRequest
│   │   │   ├── completeAuthorize() -> DirectPostCompletePurchaseRequest
│   │   │   ├── completePurchase() -> DirectPostCompletePurchaseRequest
│   │   │   └── store() -> DirectPostStoreRequest
│   │   ├── Server-to-server operations
│   │   │   ├── capture() -> DirectPostCaptureRequest
│   │   │   ├── refund() -> DirectPostRefundRequest
│   │   │   └── void() -> DirectPostReversalRequest
│   │   ├── EMV 3DS order API
│   │   │   └── createEMV3DSOrder() -> EMV3DSOrderRequest
│   │   ├── Webhook/fingerprint helper
│   │   │   └── webhook() -> DirectPostWebhookRequest
│   │   ├── Security/txn behavior
│   │   │   ├── fingerprint generation + verification
│   │   │   ├── risk + EMV txnType resolution
│   │   │   └── legacy typo alias support: vefiyFingerPrint()
│   │   └── transport + timeout control for server-to-server calls
│   │
│   ├── NABTransact_HostedPayment (HostedPaymentGateway)
│   │   ├── purchase() -> HostedPaymentPurchaseRequest
│   │   ├── completePurchase() -> HostedPaymentCompletePurchaseRequest
│   │   ├── payment alert configuration (paymentAlertEmail)
│   │   └── return link text configuration (returnUrlText)
│   │
│   └── NABTransact_UnionPay (UnionPayGateway)
│       ├── purchase() -> UnionPayPurchaseRequest
│       └── completePurchase() -> UnionPayCompletePurchaseRequest
│
├── Response Models
│   ├── SecureXMLResponse
│   ├── DirectPostAuthorizeResponse
│   ├── DirectPostCompletePurchaseResponse
│   ├── DirectPostApiResponse
│   ├── EMV3DSOrderResponse
│   ├── HostedPaymentPurchaseResponse
│   ├── HostedPaymentCompletePurchaseResponse
│   ├── UnionPayPurchaseResponse
│   └── UnionPayCompletePurchaseResponse
│
├── Transport Layer
│   ├── TransportInterface (custom adapter contract)
│   ├── TransportResponse (status/body container)
│   ├── OmnipayHttpClientTransport (default compatibility path)
│   └── CurlTransport (framework-agnostic native cURL)
│
└── Compatibility
    ├── Existing Omnipay gateway names preserved
    ├── Existing request/response classes preserved
    ├── Additive APIs only (no removals in this modernization)
    └── Legacy typo alias kept for webhook fingerprint method
```

## Related docs

- Feature matrix: `docs/feature-matrix.md`
- Architecture: `ARCHITECTURE.md`
