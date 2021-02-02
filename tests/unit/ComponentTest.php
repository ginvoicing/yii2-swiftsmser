<?php
/**
 * Created by PhpStorm.
 * User: tarunjangra
 * Date: 01/02/2021
 * Time: 09:37
 */

class ComponentTest extends Codeception\Test\Unit
{

    public function testSelectionOfPromotionalGatewayException()
    {
        $caught = false;
        try {
            Yii::$app->set('swiftsmser', [
                'class' => '\yii\swiftsmser\Gateway',
                'gateways' => [
                ]
            ]);
            Yii::$app->swiftsmser->promotional;
        } catch (\yii\swiftsmser\exceptions\BadGatewayException $e) {
            $this->assertEquals("No promotional SMS gateway found.", $e->getMessage());
            $caught = true;
        }

        $this->assertTrue($caught, 'Caught not supported exception');
    }

    public function testSelectionOfTransactionalGatewayException()
    {
        $caught = false;
        try {
            Yii::$app->set('swiftsmser', [
                'class' => '\yii\swiftsmser\Gateway',
                'gateways' => [
                ]
            ]);
            Yii::$app->swiftsmser->transactional;
        } catch (\yii\swiftsmser\exceptions\BadGatewayException $e) {
            $this->assertEquals("No transactional SMS gateway found.", $e->getMessage());
            $caught = true;
        }

        $this->assertTrue($caught, 'Caught not supported exception');
    }

    public function testGatewaySelection()
    {
        Yii::$app->set('swiftsmser', [
            'class' => '\yii\swiftsmser\Gateway',
            'gateways' => [
                [
                    'transporter' => 'Biz2',
                    'type' => 'promotional',
                    'params' => [
                        'apiBase' => 'http://biz2.smslounge.in/api/v2/',
                        'apiKey' => '374937843',
                    ]
                ],
                [
                    'transporter' => 'ICloudMessage',
                    'type' => 'transactional',
                    'params' => [
                        'apiBase' => 'http://msg.icloudsms.com/rest/services/sendSMS/',
                        'apiKey' => '374937843',
                    ]
                ]
            ]
        ]);
        $this->assertInstanceOf(\yii\swiftsmser\transporters\ICloudMessage::class,
            \Yii::$app->swiftsmser->transactional);
        $this->assertInstanceOf(\yii\swiftsmser\transporters\Biz2::class, \Yii::$app->swiftsmser->promotional);
    }

}
