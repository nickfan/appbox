<?php

return array(
    'debug'=>false,
    'usercache'=>array(
        'singleton'=>true,
        'driver'=>'auto',   // auto | null | apc | yac | redis,
        'options'=>array(
                //'autoseq'=>array('apc','yac'),
        ),
    ),
    'conf'=>array(
    ),
    'routeconf'=>array(

    ),
    'dict'=>array(
        'singleton'=>true,
        'options'=>array(
            'packer'=>'serialize', // serialize | json | msgpack
        ),
    ),
    'path'=>array(
        'base'=>__DIR__,
    ),
);
