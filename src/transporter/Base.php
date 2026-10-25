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

    /**
     * Returns the curl client cleared for a new request.
     * The previous handle is dropped before reset() so that linslin/yii2-curl
     * does not call curl_close(), which is deprecated since PHP 8.5.
     *
     * @return Curl
     */
    protected function resetCurl(): Curl
    {
        $this->_curl->curl = null;
        return $this->_curl->reset();
    }

    public function __get(string $name): mixed
    {
        if (!isset($this->_attributes[$name])) {
            throw new UnknownPropertyException("\"{$name}\" is an invalid property.", 210419831);
        }
        return $this->_attributes[$name];
    }
}
