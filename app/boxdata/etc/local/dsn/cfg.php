<?php
return array (
  'root' => array (
    'init' => array (
      'dsn' => 'http://username:password@hostname/path?arg1=v1&arg2=v2#anchor1=a1&anchor2=a2',
      'timeout' => '30',
      'settings' => '',
    ),
  ),
  'my' => array (
    'init' => array (
      'dsn' => 'http://localhost',
    ),
  ),
  'mygroup' => array (
    'init' => array (
      'dsn' => 'http://localhost',
    ),
    'g0' => array (
      'dsn' => 'http://localhost',
    ),
    'g1' => array (
      'dsn' => 'http://127.0.0.1',
    ),
  ),
  'myftp' => array (
    'init' => array (
      'dsn' => 'ftp://user:pass@127.0.0.1:21/mypath/motd.txt',
    ),
  ),
  'mytest' => array (
    'init' => array (
      'dsn' => 'http://localhost',
    ),
  ),
);
