# Electronic Signature 

[back](./README.md)

You can add an electronic signature to the PDF, by using the following specific html TAG:

```
<cert
    src="/path/to/cert.pem"
    privkey="/path/to/priv.pem"
    name="sender_name"
    location="sender_location"
    reason="sender_reason"
    contactinfo="sender_contact"
>                            
/** html **/
</cert>
```

Attribute   |  Description                      |  Example 1          | Example 2
------------|-----------------------------------|---------------------|-------------------
src         | Path to the Cert                  | /www/certs/my.pem   | /www/certs/my.crt
privkey     | Private key of the Cert if needed | /www/certs/priv.pem | nothing
name        | Name of the Cert                  | My.org Cert         |
location    | Country of the Cert               | France              |
reason      | Purpose of the Cert               | Invoice validation  |
contactinfo | EMail of organisation's contact   | contact@my.org      |

/** HTML **/ 
part could be any HTML formatted string, img, etc...

[back](./README.md)
