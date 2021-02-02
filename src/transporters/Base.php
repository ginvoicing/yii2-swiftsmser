<?php
/**
 * Created by PhpStorm.
 * User: tarunjangra
 * Date: 01/02/2021
 * Time: 06:41
 */

namespace yii\swiftsmser\transporters;

use yii\swiftsmser\exceptions\InvalidPropertyException;

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
            throw new InvalidPropertyException("\"{$name}\" is an invalid property.", 210419831);
        }
        return $this->_attributes[$name];
    }
}
