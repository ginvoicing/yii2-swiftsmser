<?php
/**
 * Created by PhpStorm.
 * User: tarunjangra
 * Date: 01/02/2021
 * Time: 06:41
 */

namespace yii\swiftsmser\transporters;

abstract class Base
{
    protected $_attributes;
    public function __construct(array $params)
    {
        $this->_attributes = $params;
    }

    public function __get($name)
    {
        return $this->_attributes[$name];
    }
}
