<?php
/**
 * Created by PhpStorm.
 * User: tarunjangra
 * Date: 01/02/2021
 * Time: 08:33
 */

namespace yii\swiftsmser\logging\models;

use yii\db\ActiveRecord;

class Sql extends ActiveRecord
{
    public static function getDb()
    {
        return \Yii::$app->swiftsmser->getLogger()->getConnection();
    }

    public static function tableName()
    {
        return \Yii::$app->swiftsmser->getLogger()->getTableName();
    }

    public function rules()
    {
        return [
            [['phone', 'message'], 'required'],
            [['response_id'], 'string', 'max' => 40],
            [['phone'], 'string', 'max' => 25],
            [['message'], 'string', 'max' => 800],
            [['deduction'], 'number'],
            [['transporter'], 'string', 'max' => 50]
        ];
    }
}
