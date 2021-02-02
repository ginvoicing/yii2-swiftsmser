<?php
/**
 * Created by PhpStorm.
 * User: tarunjangra
 * Date: 01/02/2021
 * Time: 07:28
 */

namespace yii\swiftsmser\exceptions;

class TransporterNotFoundException extends Exception
{
    public function getName()
    {
        return 'Transporter Not Found Exception';
    }
}
