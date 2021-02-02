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
                'transporters' => []
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
                'transporters' => []
            ]);
            Yii::$app->swiftsmser->transactional;
        } catch (\yii\swiftsmser\exceptions\BadGatewayException $e) {
            $this->assertEquals(210419832, $e->getCode());
            $caught = true;
        }

        $this->assertTrue($caught, 'Caught not supported exception');
    }

    public function testGatewaySelection()
    {
        Yii::$app->set('swiftsmser', [
            'class' => '\yii\swiftsmser\Gateway',
            'transporters' => [
                [
                    'class' => '\yii\swiftsmser\transporter\Biz2',
                    'type' => 'promotional'
                ],
                [
                    'class' => '\yii\swiftsmser\transporter\ICloudMessage',
                    'type' => 'transactional'
                ]
            ]
        ]);
        $this->assertInstanceOf(\yii\swiftsmser\transporter\ICloudMessage::class,\Yii::$app->swiftsmser->transactional);
        $this->assertInstanceOf(\yii\swiftsmser\transporter\Biz2::class, \Yii::$app->swiftsmser->promotional);
    }

}
