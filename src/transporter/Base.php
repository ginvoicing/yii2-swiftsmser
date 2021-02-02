<?php
/**
 * Created by PhpStorm.
 * User: tarunjangra
 * Date: 01/02/2021
 * Time: 06:41
 */

namespace yii\swiftsmser\transporter;

use yii\base\UnknownPropertyException;

abstract class Base
{
    protected $_attributes;
    public function __construct(array $params)
    {
        $this->_attributes = $params;
    }

    public function __get($name)
    {
        if (!isset($this->_attributes[$name])) {
            throw new UnknownPropertyException("\"{$name}\" is an invalid property.", 210419831);
        }
        return $this->_attributes[$name];
    }
}
