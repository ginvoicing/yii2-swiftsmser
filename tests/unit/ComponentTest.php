<?php
/**
 * Created by PhpStorm.
 * User: tarunjangra
 * Date: 01/02/2021
 * Time: 09:37
 */

class ComponentTest extends Codeception\Test\Unit
{
    use \Codeception\AssertThrows;

    protected function _before()
    {
        Yii::$app->set('swiftsmser', [
            'class' => '\yii\swiftsmser\Gateway',
            'senderId' => 'GINVCN',
            'transporters' => [
                [
                    'class' => '\yii\swiftsmser\transporter\Biz2',
                    'type' => 'promotional',
                    'params' => [
                        'apiKey' => '32423423'
                    ]
                ],
                [
                    'class' => '\yii\swiftsmser\transporter\ICloudMessage',
                    'type' => 'transactional',
                    'params' => [
                        'apiKey' => '234234234'
                    ]
                ]
            ]
        ]);
    }

    public function testBadGatewayException()
    {
        Yii::$app->set('swiftsmser', [
            'class' => '\yii\swiftsmser\Gateway',
            'transporters' => []
        ]);
        $this->assertThrows(\yii\swiftsmser\exceptions\BadGatewayException::class, function(){
            Yii::$app->swiftsmser->transactional->unknown;
        });
        $this->assertThrows(\yii\swiftsmser\exceptions\BadGatewayException::class, function(){
            Yii::$app->swiftsmser->promotional->unknown;
        });
    }

    public function testUnknownPropertyException()
    {
        $this->assertThrows(\yii\base\UnknownPropertyException::class, function(){
            Yii::$app->swiftsmser->transactional->unknown;
        });
        $this->assertThrows(\yii\base\UnknownPropertyException::class, function(){
            Yii::$app->swiftsmser->promotional->unknown;
        });
    }

    public function testGatewaySelection()
    {
        $this->assertInstanceOf(\yii\swiftsmser\transporter\ICloudMessage::class,\Yii::$app->swiftsmser->transactional);
        $this->assertInstanceOf(\yii\swiftsmser\transporter\Biz2::class, \Yii::$app->swiftsmser->promotional);
    }

}
