<?php
/**
 * Created by PhpStorm.
 * User: tarunjangra
 * Date: 01/02/2021
 * Time: 06:38
 */
namespace yii\swiftsmser\transporter;

use yii\swiftsmser\TransporterInterface;

class Biz2 extends Base implements TransporterInterface
{
    private $_baseApi = 'http://biz2.smslounge.in/api/v2/';
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
