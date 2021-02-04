<?php
/**
 * Created by PhpStorm.
 * User: tarunjangra
 * Date: 01/02/2021
 * Time: 07:28
 */

namespace yii\swiftsmser\exceptions;

class TimeLimitException extends Exception
{
    public function getName()
    {
        return 'Bad SMS Time';
    }
}
