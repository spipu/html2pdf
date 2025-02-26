# Security

[back](./README.md)

Html2Pdf is using the default [Security](../src/Security/Security.php) service to protect the external included files (CSS, images, ...).

It allows : 

 * HTTP/HTTPS external files
 * Local Files

It does **not** protect again **Blind SSRF**. This means that the library loads external resources
without validating the destination address before sending an HTTP request.

This is not the responsibility of this library.

You must ensure that the HTML you want to convert is secure, **especially if it is generated from uncontrolled data contributed by users**.
In such cases, an attacker could send requests to both external servers and restricted-access servers (e.g., within a local network).

If you need additional security, you can implement the [SecurityInterface](../src/Security/SecurityInterface.php),
and call the method `setSecurityService` on the Html2Pdf object to use it.
