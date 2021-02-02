<?php
/**
 * Created by PhpStorm.
 * User: tarunjangra
 * Date: 01/02/2021
 * Time: 09:55
 */

namespace yii\swiftsmser;

use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\swiftsmser\exceptions\BadGatewayException;

class Gateway extends Component
{
    public $gateways;

    public function __construct($config = [])
    {
        $this->gateways=$config['gateways'];
        parent::__construct($config);
    }

    public function getPromotional()
    {
        $promotional_gateways = [];
        foreach ($this->gateways as $gateway) {
            if ($gateway['type'] === 'promotional') {
                $promotional_gateways[]=$gateway;
            }
        }
        if (count($promotional_gateways)) {
            $selected_gateway = $promotional_gateways[array_rand($promotional_gateways)];
            if (isset($selected_gateway['transporter'])) {
                /** @var  $transporter */
                $transporter = "\\yii\\swiftsmser\\transporters\\{$selected_gateway['transporter']}";

                return new $transporter($selected_gateway['params']);
            }
        }
        throw new BadGatewayException('No promotional SMS gateway is avaialble.');
    }

    public function getTransactional()
    {
        $transactional_gateway = [];
        foreach ($this->gateways as $gateway) {
            if ($gateway['type'] === 'transactional') {
                $transactional_gateway[]=$gateway;
            }
        }
        if (count($transactional_gateway)) {
            $selected_gateway = $transactional_gateway[array_rand($transactional_gateway)];
            if (isset($selected_gateway['transporter'])) {
                /** @var  $transporter */
                $transporter = "\\yii\\swiftsmser\\transporters\\{$selected_gateway['transporter']}";

                return new $transporter($selected_gateway['params']);
            }
        }
        throw new BadGatewayException('No transactional SMS gateway is available.');
    }
}
