SimpleSAMLphp OAuth2 module
====================================

## Installation

This package add support for the OAuth2 protocol through a SimpleSAMLphp module
installable through [Composer](https://getcomposer.org/). Installation can be as
easy as executing:

```
composer.phar require sgomez/simplesamlphp-module-oauth2 1.0.0 # for SSP < 1.14
composer.phar require sgomez/simplesamlphp-module-oauth2 ~1.0 # for SSP >= 1.14
composer.phar require sgomez/simplesamlphp-module-oauth2 ~2.0 # for SSP >= 2.0|master
```

## Configuration

This module requires [sgomez/simplesamlphp-module-dbal](https://github.com/sgomez/simplesamlphp-module-dbal)
module configured. It's installed as a dependency but you need to read the module info and configure it.
 
### Create the schema
 
You need to run this to create the schema using the DBAL store module:
 
```
bash$ vendor/bin/dbalschema
```

### Configure the module

Copy the template file to the config directory:

```
cp modules/oauth2/config-template/module_oauth2.php config/
```

and edit it. The options are self explained.

## Create oauth2 clients

To add and remove Oauth2 clients, you need to logon on simplesaml with an admin account. Open the _Federation_ tab
and you will see the _OAuth2 Client Registry_ option.

You can specify as many redirect address as you want.

## Using the module

This module is based on [Oauth2 Server from the PHP League](https://oauth2.thephpleague.com/) and supports implicit and explicit tokens.

### Create the oauth2 keys:

The oauth2 library used generates Json Web Tokens to create the Access Tokens, so you need to create a public and private cert keys:

To generate the private key run this command on the terminal:

```
openssl genrsa -out cert/oauth2_module.pem 1024
```

If you want to provide a passphrase for your private key run this command instead:

```
openssl genrsa -passout pass:_passphrase_ -out cert/oauth2_module.pem 1024
```

then extract the public key from the private key:

```
openssl rsa -in cert/oauth2_module.pem -pubout -out cert/oauth2_module.crt
```
or use your passphrase if provided on private key generation:

```
openssl rsa -in cert/oauth2_module.pem -passin pass:_passphrase_ -pubout -out cert/oauth2_module.crt
```

If you use a passphrase remember to configure it in the _module_oauth2.php_ config file.

### Explicit Token

To ask an explicit token see the [Authorization Code Grant](https://oauth2.thephpleague.com/authorization-server/auth-code-grant/)
help page to know the parameters than you need to send (see Part One).

The address to the authorization server is: _{{baseurlpath}}/module.php/oauth2/authorize.php_

Now you need to ask for an access token. See the [Part Two](https://oauth2.thephpleague.com/authorization-server/auth-code-grant/).

The address to the access token server is: _{{baseurlpath}}/module.php/oauth2/access_token.php_

### Implicit Token

To ask an implicit token see the [Implicit Grant](https://oauth2.thephpleague.com/authorization-server/implicit-grant/)
help page to know the parameters than you need to send.

The address to the authorization server is: _{{baseurlpath}}/module.php/oauth2/authorize.php_

### Take the attributes

To recover the user attributes you need to send and `Authorization` header with the Access Token as
a Bearer Token to the userinfo page: _{{baseurlpath}}/module.php/oauth2/userinfo.php_

Example:

```
curl --request GET \
  --url http://server.com/simplesaml/module.php/oauth2/userinfo.php \
  --header 'authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1Ni...'
```
