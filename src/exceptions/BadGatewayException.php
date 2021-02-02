<?php
/**
 * Created by PhpStorm.
 * User: tarunjangra
 * Date: 01/02/2021
 * Time: 07:28
 */

namespace yii\swiftsmser\exceptions;

class BadGatewayException extends Exception
{
    public function getName()
    {
        return 'Bad SMS Gateway Exception';
    }
}
