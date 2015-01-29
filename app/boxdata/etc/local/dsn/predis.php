<?php
return array (
  'root' => array (
    'init' =>  array (
      'parameters' => 'tcp://127.0.0.1:6379?alias=master',
      'options'=>array(),
    ),
  ),
  'my' => array (
    'init' => array (
      'parameters' => 'tcp://127.0.0.1:6379?alias=master',
      'options'=>array(),
    ),
  ),
  'mygroup' => array (
    'init' => array (
      'parameters' => 'tcp://127.0.0.1:6379?alias=master',
      'options'=>array(),
    ),
    'g0' => array (
      'parameters' => 'tcp://127.0.0.1:6379?alias=master',
      'options'=>array(),
    ),
    'g1' => array (
      'parameters' => 'tcp://127.0.0.1:6379?alias=master',
      'options'=>array(),
    ),
  ),
);
