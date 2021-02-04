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

    protected function _before()
    {
        Yii::$app->set('swiftsmser', [
            'class' => \yii\swiftsmser\Gateway::class,
            'senderId' => 'GINVCN',
            'transporters' => [
                [
                    'class' => \yii\swiftsmser\transporter\Biz2::class,
                    'type' => \yii\swiftsmser\enum\Type::TRANSACTIONAL(),
                    'params' => [
                        'apiKey' => '17003704045822bffef9130561b72353'
                    ]
                ],
                [
                    'class' => \yii\swiftsmser\transporter\ICloudMessage::class,
                    'type' => \yii\swiftsmser\enum\Type::PROMOTIONAL(),
                    'params' => [
                        'apiKey' => '56f5a5be78d397d3b45925a3f657158'
                    ]
                ]
            ]
        ]);
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
                ->setTemplateID('34a7b0e7-58cd-40b5-a3d9-c901948ec33d')
                ->setVariables(["Tarun Jangra","EST-001","Rs. 334.11","Hello Communication"])
                ->setEntityId('1101147480000010561')
                ->setHeaderId('1105158201172710267')
            , ['9888300750']);

        /*Yii::$app->swiftsmser->transactional->send(
            (new \yii\swiftsmser\SMSPacket())
                ->setBody(
                    "Thank you for your payment. We have received {#var#} against your balance fee. Thanks again, {#var#} ginvoicing.com",
                    ["Rs 344.3", "HelloCommunication"]
                )
                ->setTemplateID('1107161061558263764')
                ->setEntityId('1101147480000010561')
                ->setHeaderId('1105158201172710267')
            , ['9888300750']);*/
        $this->assertTrue($response->getStatus() == \yii\swiftsmser\enum\Status::SUCCESS());
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
            , ['9888300750']);
        /** @var \yii\swiftsmser\ResponseInterface $response */
        $this->assertTrue($response->getStatus() == \yii\swiftsmser\enum\Status::SUCCESS());
    }

}
