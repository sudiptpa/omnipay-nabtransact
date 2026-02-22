# Architecture

## Overview

`omnipay-nabtransact` remains an Omnipay gateway package and keeps full Omnipay request/response compatibility.

This modernization adds a transport layer so the package can run with:

- Omnipay HTTP client (default, backward compatible)
- Native cURL transport (framework-agnostic alternative)
- Custom user transport via contract

## Design Goals

- Preserve Omnipay API surface and existing integrations.
- Keep non-breaking behavior for request classes and gateway methods.
- Improve reliability and testability through explicit transport contracts.
- Keep dependencies minimal.

## Project Map

```text
src/
  DirectPostGateway.php
  SecureXMLGateway.php
  HostedPaymentGateway.php
  UnionPayGateway.php

  Message/
    *Request.php
    *Response.php

  Transport/
    TransportInterface.php
    TransportResponse.php
    OmnipayHttpClientTransport.php
    CurlTransport.php

  Enums/
    TransactionType.php
```

## Runtime Flows

### 1) SecureXML requests

1. Gateway creates request through Omnipay (`authorize/purchase/capture/refund/echoTest`).
2. Request builds XML payload.
3. Request resolves transport in this order:
   - explicit request transport (`setTransport()`)
   - Omnipay adapter transport (default)
4. Response XML is mapped to `SecureXMLResponse`.

### 2) DirectPost / UnionPay

- Redirect-based requests remain unchanged.
- Fingerprint verification stays compatible.
- Backward-compat typo alias (`vefiyFingerPrint`) remains supported.
- DirectPost supports purchase, authorize, complete callbacks, store-only flow, and additive server-to-server operations (`capture`, `refund`, `void`).
- EMV 3DS order management is exposed via `createEMV3DSOrder()`.

### 3) Hosted Payment

- Redirect form flow remains unchanged.
- Environment endpoint selection now aligns correctly with `testMode`.

## Extension Points

- Implement `TransportInterface` for custom HTTP stacks.
- Inject custom transport per request via `setTransport($transport)`.

## Backward Compatibility

- Omnipay gateway class names and behavior remain intact.
- Existing request/response classes remain available.
- Deprecated typo method remains callable.

## Testing Strategy

- Request/response behavior tests for existing Omnipay flows.
- Dedicated tests for transport injection and endpoint correctness.
- Fingerprint verification coverage (success/failure).
