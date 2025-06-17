# Security

[back](./README.md)

Html2Pdf is using the default [Security](../src/Security/Security.php) service to protect the external included files (CSS, images, ...).

It allows : 

 * HTTP/HTTPS external files
 * Local Files

You can add a specific host to be allowed for http/https scheme. By default, the whitelist is empty.

```php
$html2pdf->getSecurityService()->addAllowedHost('www.html2pdf.fr');
```

You can reset the list of allowed hosts for http/https scheme.

```php
$html2pdf->getSecurityService()->resetAllowedHosts();
```

You can disable the check on the allowed hosts for http/https scheme.

```php
$html2pdf->getSecurityService()->disableCheckAllowedHosts();
```

You must ensure that the HTML you want to convert is secure, **especially if it is generated from uncontrolled data contributed by users**.
In such cases, an attacker could send requests to both external servers and restricted-access servers (e.g., within a local network) on host that you have added to the whitelist.

If you need additional security, you can implement the [SecurityInterface](../src/Security/SecurityInterface.php),
and call the method `setSecurityService` on the Html2Pdf object to use it.
