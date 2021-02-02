<?php
/**
 * Created by PhpStorm.
 * User: tarunjangra
 * Date: 01/02/2021
 * Time: 06:38
 */
namespace yii\swiftsmser\transporters;

use yii\swiftsmser\TransporterInterface;

class ICloudMessage extends Base implements TransporterInterface
{
    public function getBalance()
    {
        // TODO: Implement getBalance() method.
    }

    public function getMessageStatus($id, $phone, $all = 2)
    {
        // TODO: Implement getMessageStatus() method.
    }

    public function sendMessage(array $params)
    {
        // TODO: Implement sendMessage() method.
    }
}
