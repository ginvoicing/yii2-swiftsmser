<?php
/**
  * Created by PhpStorm.
 * User: tarunjangra
 * Date: 01/02/2021
 * Time: 09:31
 */

namespace data;

use yii\db\ActiveRecord;

class SqlModel extends ActiveRecord
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
            [['type', 'send_time', 'status', 'error'], 'integer'],
            [['sms_id'], 'string', 'max' => 40],
            [['phone'], 'string', 'max' => 25],
            [['message'], 'string', 'max' => 800],
            [['cost'], 'number'],
            [['operator'], 'string', 'max' => 50],
            [['region'], 'string', 'max' => 150],
        ];
    }
}
