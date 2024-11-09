<?php
/**
 * Created by PhpStorm.
 * User: tarunjangra
 * Date: 01/02/2021
 * Time: 06:41
 */

namespace yii\swiftsmser\transporter;

use linslin\yii2\curl\Curl;
use yii\base\UnknownPropertyException;

abstract class Base
{
    protected $_attributes;
    protected $_curl;
    protected $_senderId;

    public string $type;
    protected $_delimiter = ',';

    public function __construct(string $sender_id, Curl $curl, $config = [])
    {
        $this->_attributes = $config;
        $this->_senderId = $sender_id;
        $this->_curl = $curl;
    }

    public function __get(string $name):string
    {
        if (!isset($this->_attributes[$name])) {
            throw new UnknownPropertyException("\"{$name}\" is an invalid property.", 210419831);
        }
        return $this->_attributes[$name];
    }
}
