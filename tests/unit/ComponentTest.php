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
            $this->assertEquals(210419832, $e->getCode());
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
            $this->assertEquals(210419833, $e->getCode());
            $caught = true;
        }

        $this->assertTrue($caught, 'Caught not supported exception');
    }


    public function testInvalidPropertyException()
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
        $caught = false;
        try {
            /*
             * This property is not defined in params of the component settings.
             */
            \Yii::$app->swiftsmser->transactional->base;
        }catch (\yii\swiftsmser\exceptions\InvalidPropertyException $e) {
            $this->assertEquals(210419831, $e->getCode());
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
