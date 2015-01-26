<?php
return array (
  'root' => array (
    'init' => array (
      'redisHost' => '127.0.0.1:6379',
      'redisPersistent' => '1',
      'redisConnectTimeout' => '',
      'redisOptsSerializer' => '',
    ),
  ),
  'my' => array (
    'init' => array (
      'redisHost' => '127.0.0.1:6379',
    ),
  ),
  'mygroup' => array (
    'init' => array (
      'redisHost' => '127.0.0.1:6379',
    ),
    'g0' => array (
      'redisHost' => '127.0.0.1:6379',
    ),
    'g1' => array (
      'redisHost' => '127.0.0.1:6379',
    ),
  ),
);
