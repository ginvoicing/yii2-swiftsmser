<?php
/**
 * Created by PhpStorm.
 * User: tarunjangra
 * Date: 01/02/2021
 * Time: 06:38
 */
namespace yii\swiftsmser\transporters;

use yii\swiftsmser\TransporterInterface;

class Biz2 extends Base implements TransporterInterface
{
    public function getBalance():float
    {
        // TODO: Implement getBalance() method.
    }

    public function sendMessage(array $params):string
    {
        // TODO: Implement sendMessage() method.
    }

    public function getMessageStatus($id, $phone, $all = 2):array
    {
        // TODO: Implement getMessageStatus() method.
    }
}