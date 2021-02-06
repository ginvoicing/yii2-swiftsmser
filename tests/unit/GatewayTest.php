<?php
/**
 * Created by PhpStorm.
 * User: tarunjangra
 * Date: 01/02/2021
 * Time: 09:37
 */

use \yii\swiftsmser\transporter\Biz2;
use \yii\swiftsmser\transporter\ICloudMessage;
use yii\swiftsmser\Gateway;

class GatewayTest extends Codeception\Test\Unit
{
    use \Codeception\AssertThrows;

    protected function _before(): void
    {
        Yii::$app->get('db',)->dsn = 'mysql:host=127.0.0.1;port=' . $_ENV['MYSQL_PORT'] . ';dbname=smser';
        Yii::$app->set('swiftsmser', [
            'class' => \yii\swiftsmser\Gateway::class,
            'senderId' => 'GINVCN',
            'headerId' => '1105158201172710267',
            'entityId' => '1101147480000010561',
            'logging' => [
                'connection' => 'db',
                'tableName' => 'ginni_sms_logger'
            ],
            'transporters' => [
                [
                    'class' => \yii\swiftsmser\transporter\Biz2::class,
                    'type' => \yii\swiftsmser\enum\Type::TRANSACTIONAL(),
                    'params' => [
                        'apiKey' => $_ENV['BIZ2_API_KEY']
                    ]
                ],
                [
                    'class' => \yii\swiftsmser\transporter\ICloudMessage::class,
                    'type' => \yii\swiftsmser\enum\Type::PROMOTIONAL(),
                    'params' => [
                        'apiKey' => $_ENV['ICLOUD_API_KEY']
                    ]
                ]
            ]
        ]);
        parent::_before();
    }

    public function testMysqlConnection()
    {
        if (\Yii::$app->get('swiftsmser')->logging) {
            \Yii::$app->runAction('migrate/up', [
                'interactive' => 0
            ]);
        }
    }

    public function testInvalidConfigurationException()
    {
        Yii::$app->set('swiftsmser', [
            'class' => Gateway::class
        ]);

        $this->assertThrows(yii\base\InvalidConfigException::class, function () {
            Yii::$app->swiftsmser->senderId;
        });

        $this->assertThrows(yii\base\InvalidConfigException::class, function () {
            Yii::$app->swiftsmser->unicodeSMSCharLength;
        });
        $this->assertThrows(yii\base\InvalidConfigException::class, function () {
            Yii::$app->swiftsmser->normalCharLength;
        });
        $this->assertThrows(yii\base\InvalidConfigException::class, function () {
            Yii::$app->swiftsmser->headerId;
        });
        $this->assertThrows(yii\base\InvalidConfigException::class, function () {
            Yii::$app->swiftsmser->entityId;
        });
    }

    public function testUnknownPropertyException()
    {
        $this->assertThrows(\yii\base\UnknownPropertyException::class, function () {
            Yii::$app->swiftsmser->transactional->unknown;
        });

        $this->assertThrows(\yii\base\UnknownPropertyException::class, function () {
            Yii::$app->swiftsmser->promotional->unknown;
        });
    }

    public function testNotfoundTransporterException()
    {
        Yii::$app->get('swiftsmser')->transporters = [

            [
                'class' => '\yii\swiftsmser\transporter\Unknown',
                'type' => \yii\swiftsmser\enum\Type::PROMOTIONAL(),
                'params' => [
                    'apiKey' => '32423423'
                ]
            ]
        ];
        $this->assertThrows(\yii\swiftsmser\exceptions\TransporterNotFoundException::class, function () {
            Yii::$app->swiftsmser->promotional;
        });
    }

    public function testGatewaySelection()
    {
        $this->assertInstanceOf(ICloudMessage::class, Yii::$app->swiftsmser->promotional->transporter);
        $this->assertInstanceOf(Biz2::class, Yii::$app->swiftsmser->transactional->transporter);
    }

    public function testBalanceFromPromotionalGateway()
    {
        $this->assertIsInt(Yii::$app->swiftsmser->promotional->balance, "Valid response from the gateway");
    }

    public function testBalanceFromTransactionalGateway()
    {
        $this->assertIsInt(Yii::$app->swiftsmser->transactional->balance, "Valid response from the gateway");
    }


    public function testSendTransactionalSMS()
    {
        /** @var \yii\swiftsmser\SMSPacket $smsPacket */
        $smsPacket = \Yii::createObject([
            'class' => \yii\swiftsmser\SMSPacket::class,
            'templateId' => '1107161061671432172',
            'body' => 'Dear {#var#}, There is an estimate: {#var#} of {#var#}. For more details {#var#} Thank You, {#var#} ginvoicing.com',
            'variables' => ["Hansika Jangra", "EST-213", "Rs. 45.21", "https://ginvcn.in/iud2", "universal Communication"],
            'to' => ['9888300750']
        ]);

        /** @var \yii\swiftsmser\ResponseInterface $response */
        $response = Yii::$app->swiftsmser->transactional->send($smsPacket);
        $this->assertTrue($response->getStatus() == \yii\swiftsmser\enum\Status::SUCCESS());
    }

    public function testDeductionOfSMS()
    {
        /** @var \yii\swiftsmser\SMSPacket $smsPacket */
        $smsPacket = \Yii::createObject([
            'class' => \yii\swiftsmser\SMSPacket::class,
            'templateId' => '1107161061671432172',
            'body' => 'Dear {#var#}, There is a new invoice: {#var#} of {#var#}. For more details {#var#} Thank You, {#var#} ginvoicing.com',
            'variables' => ["Deepak kumar", "INV-0013", "Rs 344.3", "https://gnvcn.in/44asj3", "HelloCommunication"],
            'to' => ['9888300750']
        ]);
        $this->assertTrue($smsPacket->deduction > 0,"Deduction is {$smsPacket->deduction}");
    }

    /**
     * @skip
     */
    public function testSendTransactionalSMSWithTemplateAPI()
    {
        /** @var \yii\swiftsmser\ResponseInterface $response */
        /*$response = Yii::$app->swiftsmser->transactional->send(
             (new \yii\swiftsmser\SMSPacket())
                 ->setTemplateId('34a7b0e7-58cd-40b5-a3d9-c901948ec33d')
                 ->setBody(
                     "Dear {#var#}, There is an estimate: {#var#} of {#var#}. For more details {#var#} Thank You, {#var#} ginvoicing.com",
                     ["Deepak Jangra","EST-004","Rs. 424.11","https://ginvcn.in/34324","Raj Communication"]
                 )
                 ->setEntityId('1101147480000010561')
                 ->setHeaderId('1105158201172710267')
                 ->setDeduction(2)
             , ['9888300750']);*/

    }

    public function testSendPromotionalSMS()
    {
        /** @var \yii\swiftsmser\SMSPacket $smsPacket */
        $smsPacket = \Yii::createObject([
            'class' => \yii\swiftsmser\SMSPacket::class,
            'templateId' => '1107161061675566196',
            'body' => 'Dear {#var#}, There is a new invoice: {#var#} of {#var#}. For more details {#var#} Thank You, {#var#} ginvoicing.com',
            'variables' => ["Deepak kumar", "INV-0013", "Rs 344.3", "https://gnvcn.in/44asj3", "HelloCommunication"],
            'to' => ['9888300750']
        ]);

        $response = Yii::$app->swiftsmser->promotional->send($smsPacket);
        /** @var \yii\swiftsmser\ResponseInterface $response */
        $this->assertTrue($response->getStatus() == \yii\swiftsmser\enum\Status::SUCCESS());
    }
}
