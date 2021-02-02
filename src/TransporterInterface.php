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

interface TransporterInterface
{
    public function __construct(array $params);

    /**
     * Get user balance
     * @throws BalanceException
     * @return float|bool
     */
    public function getBalance();

    /**
     * @param array $params
     * @throws SendException
     * @return string
     */
    public function sendMessage(array $params);

    /**
     * @param string $id
     * @param string $phone
     * @param int $all
     * @return array
     */
    public function getMessageStatus($id, $phone, $all = 2);
}
