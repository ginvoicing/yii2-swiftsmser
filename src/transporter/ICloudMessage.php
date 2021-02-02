<?php
/**
 * Created by PhpStorm.
 * User: tarunjangra
 * Date: 01/02/2021
 * Time: 06:38
 */
namespace yii\swiftsmser\transporter;

use yii\swiftsmser\TransporterInterface;

class ICloudMessage extends Base implements TransporterInterface
{
    /** @var string */
    private $_apiBase = 'http://msg.icloudsms.com/rest/services/sendSMS/';
    /** @var string */
    private $_apiKey = '374937843';


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
