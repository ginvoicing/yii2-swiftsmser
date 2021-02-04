<?php
/**
 * Created by PhpStorm.
 * User: tarunjangra
 * Date: 02/02/2021
 * Time: 23:56
 */

namespace yii\swiftsmser\enum;

use MyCLabs\Enum\Enum;

class Type extends Enum
{
    private const PROMOTIONAL = 'promotional';
    private const TRANSACTIONAL = 'transactional';

    /**
     * @return self
     */
    public static function PROMOTIONAL(): self
    {
        return new Type(self::PROMOTIONAL);
    }

    /**
     * @return self
     */
    public static function TRANSACTIONAL(): self
    {
        return new Type(self::TRANSACTIONAL);
    }
}
