<?php
/**
 * Created by PhpStorm.
 * User: tarunjangra
 * Date: 01/02/2021
 * Time: 08:31
 */

namespace yii\swiftsmser\logging;

use yii\base\InvalidConfigException;
use yii\base\BaseObject;
use yii\di\Instance;
use yii\db\Connection as SqlConnection;
use yii\swiftsmser\logging\models\Sql;

class Logger extends BaseObject implements LoggerInterface
{
    /**
     * @var string
     */
    public $tableName = '{{%swiftsmser_logger}}';

    /**
     * @var array|string|\yii\db\Connection
     */
    public $connection = null;

    /**
     * Log table model
     * @var Sql
     */
    private $_model;

    public function init()
    {
        $this->connection = Instance::ensure($this->connection);

        if ($this->connection instanceof SqlConnection) {
            $this->_model = Sql::class;
        } else {
            throw new InvalidConfigException("This connections doesn't support.");
        }
    }

    public function getTableName()
    {
        return $this->tableName;
    }

    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @inheritdoc
     */
    public function setRecord($data)
    {
        $record = new $this->_model();
        foreach ($data as $key => $value) {
            if ($record->hasAttribute($key)) {
                $record->$key = $value;
            }
        }

        return $record->save();
    }

    /**
     * @inheritdoc
     */
    public function updateRecordBySmsId($response_id, $data)
    {
        if (!empty($response_id)) {
            $record = new $this->_model();
            $record = $record->findOne(['response_id' => $response_id]);
            if ($record) {
                foreach ($data as $key => $value) {
                    if ($record->hasAttribute($key)) {
                        $record->$key = $value;
                    }
                }

                return $record->save();
            }
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function updateRecordBySmsIdAndPhone($response_id, $phone, $data)
    {
        if (!empty($sms_id)) {
            $record = new $this->_model();
            $record = $record->findOne(['response_id' => $response_id, 'phone' => $phone]);
            if ($record) {
                foreach ($data as $key => $value) {
                    if ($record->hasAttribute($key)) {
                        $record->$key = $value;
                    }
                }

                return $record->save();
            }
        }

        return false;
    }
}
