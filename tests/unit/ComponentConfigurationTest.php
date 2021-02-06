<?php
/**
 * Created by PhpStorm.
 * User: tarunjangra
 * Date: 01/02/2021
 * Time: 09:37
 */

use \yii\swiftsmser\transporter\ICloudMessage;
use \yii\swiftsmser\transporter\Biz2;
use \yii\swiftsmser\enum\Type;
use \yii\swiftsmser\Gateway;


class ComponentConfigurationTest extends Codeception\Test\Unit
{
    use \Codeception\AssertThrows;

    protected function _before()
    {
        Yii::$app->set('swiftsmser', [
            'class' => Gateway::class,
            'senderId' => 'GINVCN',
            'transporters' => [
                [
                    'class' => Biz2::class,
                    'type' => Type::TRANSACTIONAL(),
                    'params' => [
                        'apiKey' => '234234'
                    ]
                ],
                [
                    'class' => ICloudMessage::class,
                    'type' => Type::PROMOTIONAL(),
                    'params' => [
                        'apiKey' => '2342342'
                    ]
                ]
            ]
        ]);
    }

    public function testBadGatewayException()
    {
        Yii::$app->set('swiftsmser', [
            'class' => Gateway::class,
            'transporters' => [],
            'senderId' => ''
        ]);
        $this->assertThrows(yii\base\InvalidConfigException::class, function () {
            Yii::$app->swiftsmser->transactional;
        });
        $this->assertThrows(yii\base\InvalidConfigException::class, function () {
            Yii::$app->swiftsmser->promotional;
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
        Yii::$app->set('swiftsmser', [
            'class' => Gateway::class,
            'senderId' => 'GINVCN',
            'transporters' => [
                [
                    'class' => '\yii\swiftsmser\transporter\Unknown',
                    'type' => \yii\swiftsmser\enum\Type::PROMOTIONAL(),
                    'params' => [
                        'apiKey' => '32423423'
                    ]
                ]
            ]
        ]);
        $this->assertThrows(\yii\swiftsmser\exceptions\TransporterNotFoundException::class, function () {
            Yii::$app->swiftsmser->promotional;
        });
    }

    public function testGatewaySelection()
    {
        $this->assertInstanceOf(ICloudMessage::class, Yii::$app->swiftsmser->promotional->transporter);
        $this->assertInstanceOf(Biz2::class, Yii::$app->swiftsmser->transactional->transporter);
    }

}
