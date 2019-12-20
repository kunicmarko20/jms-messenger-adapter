JMS Messenger Adapter
=====================

Use JMS Serializer with Symfony Messenger.

THIS IS 0.X BRANCH, SUPPORTING ONLY SYMFONY 4.3, for newer versions check [here](https://github.com/kunicmarko20/jms-messenger-adapter/blob/master/README.md).

[![PHP Version](https://img.shields.io/badge/php-%5E7.2-blue.svg)](https://img.shields.io/badge/php-%5E7.2-blue.svg)
[![Latest Stable Version](https://poser.pugx.org/kunicmarko/jms-messenger-adapter/v/stable)](https://packagist.org/packages/kunicmarko/jms-messenger-adapter)
[![Latest Unstable Version](https://poser.pugx.org/kunicmarko/jms-messenger-adapter/v/unstable)](https://packagist.org/packages/kunicmarko/jms-messenger-adapter)

[![Build Status](https://travis-ci.org/kunicmarko20/jms-messenger-adapter.svg?branch=master)](https://travis-ci.org/kunicmarko20/jms-messenger-adapter)
[![Coverage Status](https://coveralls.io/repos/github/kunicmarko20/jms-messenger-adapter/badge.svg?branch=master)](https://coveralls.io/github/kunicmarko20/jms-messenger-adapter?branch=master)

Documentation
-------------

* [Installation](#installation)
  * [Symfony](#symfony)
    * [Configuration](#configuration)
* [Stamps](#stamps)

## Installation

Add dependency with Composer:

```bash
composer require kunicmarko/jms-messenger-adapter
```

### Symfony

Enable the bundle for all environments:

```php
// bundles.php
return [
    //...
    KunicMarko\JMSMessengerAdapter\Bridge\Symfony\JMSMessengerAdapterBundle::class => ['all' => true],
];
```

#### Configuration

```yaml
#config/packages/jms_messenger.yaml
jms_messenger:
    format: json # xml, json
    serializer_id: messenger.transport.jms_serializer
```

Serialized id should be configured in the messenger config, in case you did not get the recipe, just add:

```yaml
#config/packages/jms_messenger.yaml
framework:
  messenger:
    enabled: true
    serializer:
      default_serializer: messenger.transport.jms_serializer
```

## Stamps

This library provides additional stamps that will use JMS Deserialization/Serialization Context
for serializing/deserializing messages.

```php
use JMS\Serializer\SerializationContext;
use KunicMarko\JMSMessengerAdapter\Stamp\SerializationContextStamp;

$context = SerializationContext::create();
$context->setGroups(['foo']);
        
$messageBus->dispatch(new Message(), [new SerializationContextStamp($context)]);
```
