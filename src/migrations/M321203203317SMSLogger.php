<?php
/**
 * Created by PhpStorm.
 * User: tarunjangra
 * Date: 01/02/2021
 * Time: 08:15
 */

namespace yii\swiftsmser\migrations;

class M321203203317SMSLogger extends \yii\db\Migration
{
    public $tableName = '{{%ginni_sms_logger}}';

    public function init()
    {
        /**
         * @var \yii\swiftsmser\logging\Logger $logger
         */
        $logger = \Yii::$app->swiftsmser->getLogger();
        if ($logger === false) {
            throw new Exception('Logger must be set');
        }
        $this->tableName = $logger->getTableName();
        $this->db = $logger->getConnection();
    }

    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey()->unsigned()->notNull(),
            'response_id' => $this->string(40)->null(),
            'phone' => $this->string(25)->notNull(),
            'message' => $this->string(800),
            'type' => $this->string(20)->defaultValue(0),
            'deduction' => $this->integer(3)->unsigned(),
            'status' => $this->string(20),
            'raw' => $this->text(),
            'transporter' => $this->string(100),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->append('ON UPDATE NOW()'),
            'created_at' => $this->timestamp()->defaultExpression('NOW()')
        ]);
    }

    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }
}
