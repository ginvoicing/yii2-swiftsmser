<?php
/**
 * Created by PhpStorm.
 * User: tarunjangra
 * Date: 01/02/2021
 * Time: 07:20
 */

namespace yii\swiftsmser;

use yii\swiftsmser\exceptions\BalanceException;
use yii\swiftsmser\exceptions\SendException;
use yii\validators\NumberValidator;

interface TransporterInterface
{
    /**
     * Get sms balance in user's account
     *
     * @return mixed
     * @throws BalanceException
     */
    public function getBalance();

    /**
     * Send sms to the defined user
     *
     * @param SMSPacket $packet SMS object to be sent.
     * @param array $to Array of phone numbers to be sent.
     * @return mixed
     * @throws SendException
     */
    public function send(SMSPacket &$packet, array $to = []): ResponseInterface;
}
