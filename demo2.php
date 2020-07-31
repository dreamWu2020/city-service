<?php
require "./vendor/autoload.php";

use CityService\Factory;

try {

    $config = [

    ];

    $instance = Factory::getInstance('wechat', [
        'appId' => 'test',
        'appSecret' => 'test',
        'deliveryId' => 'TEST',
        'shopId' => 'test_shop_id',
    ]);

    $result = $instance->getAllImmeDelivery();

    if($result->isSuccessful()) {
        var_dump($result->getData());
    } else {
        var_dump($result->getMessage());
    }

//    var_dump($instance->preAddOrder());
//    var_dump($instance->addOrder());
//    var_dump($instance->reOrder());
//    var_dump($instance->addTip());
//    var_dump($instance->preCancelOrder());
//    var_dump($instance->cancelOrder());
//    var_dump($instance->abnormalConfirm());
//    var_dump($instance->getOrder());
}
catch (\CityService\Exceptions\CityServiceException $e)
{
    var_dump(get_class($e). ': ' .$e->getMessage());
}
