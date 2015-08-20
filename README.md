TurtlePHP-MailChimpPlugin
======================

``` php
require_once APP . '/plugins/TurtlePHP-ConfigPlugin/Config.class.php';
require_once APP . '/vendors/mailchimp-api/src/Drewm/MailChimp.php';
require_once APP . '/plugins/TurtlePHP-MailChimpPlugin/MailChimp.class.php';
\Plugin\MailChimp::init();
```

``` php
...
\Plugin\MailChimp::setConfigPath('/path/to/config/file.inc.php');
\Plugin\MailChimp::init();
```
