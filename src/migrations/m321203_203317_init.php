<?php
/**
 * Created by PhpStorm.
 * User: tarunjangra
 * Date: 01/02/2021
 * Time: 08:15
 */

class m321203_203317_init extends \yii\db\Migration
{
    private $_tableName = '{{%swiftsmser_log}}';
    public function init()
    {
        /**
         * @var \yii\swiftsmser\logging\Logger $logger
         */
        $logger = Yii::$app->swiftsmser->getLogger();
        if ($logger === false) {
            throw new Exception('Logger must be set');
        }
        $this->_tableName = $logger->getTableName();
        $this->db = $logger->getConnection();
    }

    public function safeUp()
    {
        $this->createTable($this->_tableName, [
            'id' => $this->primaryKey()->unsigned()->notNull(),
            'sms_id' => $this->string(40)->notNull(),
            'phone' => $this->string(25)->notNull(),
            'message' => $this->string(800),
            'type' => $this->smallInteger(3)->defaultValue(0),
            'send_time' => $this->integer(11)->unsigned(),
            'cost' => $this->money(5, 2)->unsigned(),
            'status' => $this->smallInteger(3),
            'error' => $this->smallInteger(3),
            'transporter' => $this->string(50)
        ]);
    }

    public function safeDown()
    {
        $this->dropTable($this->_tableName);
    }
}
