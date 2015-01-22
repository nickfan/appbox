<?php
return array (
  'root' => array (
    'init' => array (
      'memHosts' => '127.0.0.1:11211,localhost:11211',
      'memProtocol' => 'TCP',
    ),
  ),
  'my' => array (
    'init' => array (
      'mongoHost' => '127.0.0.1:27017',
    ),
  ),
  'mygroup' => array (
    'init' => array (
      'mongoHost' => '127.0.0.1:27017',
    ),
    'g0' => array (
      'mongoHost' => '127.0.0.1:27017',
    ),
    'g1' => array (
      'mongoHost' => '127.0.0.1:27017',
    ),
  ),
);
