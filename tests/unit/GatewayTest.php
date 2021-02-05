<?php
/**
 * Created by PhpStorm.
 * User: tarunjangra
 * Date: 01/02/2021
 * Time: 09:37
 */

class GatewayTest extends Codeception\Test\Unit
{
    use \Codeception\AssertThrows;

    protected function _before(): void
    {
        Yii::$app->get('db',)->dsn = 'mysql:host=127.0.0.1;port='.$_ENV['MYSQL_PORT'].';dbname=smser';
        Yii::$app->set('swiftsmser', [
            'class' => \yii\swiftsmser\Gateway::class,
            'senderId' => 'GINVCN',
            'logging' => [
                'connection' => 'db'
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

    public function testMysqlConnection(){
        if(\Yii::$app->get('swiftsmser')->logging){
            \Yii::$app->runAction('migrate/up', [
                'migrationPath' => __DIR__.'/../../src/migrations',
                'interactive' => 0
            ]);
        }
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
        /** @var \yii\swiftsmser\ResponseInterface $response */
        $response = Yii::$app->swiftsmser->transactional->send(
            (new \yii\swiftsmser\SMSPacket())
                ->setBody(
                    "Dear {#var#}, There is an estimate: {#var#} of {#var#}. For more details {#var#} Thank You, {#var#} ginvoicing.com",
                    ["Rahul","EST-213","Rs. 45.21","https://ginvcn.in/iud2","universal Communication"]
                )
                ->setTemplateID('1107161061671432172')
                ->setEntityId('1101147480000010561')
                ->setHeaderId('1105158201172710267')
                ->setDeduction(2)
            , ['9888300750']);
        $this->assertTrue($response->getStatus() == \yii\swiftsmser\enum\Status::SUCCESS());
    }

    public function testSendTransactionalSMSWithTemplateAPI() {
        /** @var \yii\swiftsmser\ResponseInterface $response */
        /*$response = Yii::$app->swiftsmser->transactional->send(
             (new \yii\swiftsmser\SMSPacket())
                 ->setTemplateID('34a7b0e7-58cd-40b5-a3d9-c901948ec33d')
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
        $response = Yii::$app->swiftsmser->promotional->send(
            (new \yii\swiftsmser\SMSPacket())
                ->setBody(
                    "Dear {#var#}, There is a new invoice: {#var#} of {#var#}. For more details {#var#} Thank You, {#var#} ginvoicing.com",
                    ["Deepak kumar","INV-0013","Rs 344.3", "https://gnvcn.in/44asj3","HelloCommunication"])
                ->setTemplateID('1107161061675566196')
                ->setEntityId('1101147480000010561')
                ->setHeaderId('1105158201172710267')
                ->setDeduction(2)
            , ['9888300750']);
        /** @var \yii\swiftsmser\ResponseInterface $response */
        $this->assertTrue($response->getStatus() == \yii\swiftsmser\enum\Status::SUCCESS());
    }

}
