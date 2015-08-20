TurtlePHP-MailChimpPlugin
======================

``` php
require_once APP . '/plugins/TurtlePHP-ConfigPlugin/Config.class.php';
require_once APP . '/vendors/campaignmonitor-createsend-php/csrest_subscribers.php';
require_once APP . '/plugins/TurtlePHP-MailChimpPlugin/MailChimp.class.php';
\Plugin\MailChimp::init();
```

``` php
...
\Plugin\MailChimp::setConfigPath('/path/to/config/file.inc.php');
\Plugin\MailChimp::init();
```
