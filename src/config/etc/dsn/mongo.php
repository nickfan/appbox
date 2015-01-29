<?php
return array (
  'root' => array (
    'init' => array (
      'mongoHost' => '127.0.0.1:27017',
      'mongoUser' => '',
      'mongoPasswd' => '',
      'mongoReplicaSet' => '',
      'mongoConnSets' => '?readPreference=secondaryPreferred',
      'mongoSetSlaveOk' => '1',
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
