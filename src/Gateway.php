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
use yii\swiftsmser\exceptions\ClassNotFoundException;

class Gateway extends Component
{
    /** @var array */
    public $transporters;

    public function __construct($config = [])
    {
        $this->transporters=$config['transporters'];
        parent::__construct($config);
    }

    public function getPromotional(): TransporterInterface
    {
        return $this->getGateway('promotional');
    }

    public function getTransactional(): TransporterInterface
    {
        return $this->getGateway('transactional');
    }

    private function getGateway(string $type): TransporterInterface
    {
        $gateways = [];
        foreach ($this->transporters as $transporter) {
            if (isset($transporter['type']) && $transporter['type'] === $type) {
                $gateways[]=$transporter;
            }
        }
        if (count($gateways)) {
            $selected_transporter = $gateways[array_rand($gateways)];
            if (isset($selected_transporter['class']) && class_exists($selected_transporter['class'])) {
                return new $selected_transporter['class']($selected_transporter['params']);
            } else {
                throw new ClassNotFoundException("Defined transporter \"{$selected_transporter['class']}\" not found.");
            }
        }
        throw new BadGatewayException("No {$type} sms transporter is defined.", 210419832);
    }

    public function getSenderId():string
    {
        return $this->transporters['senderId'];
    }
    public function setSenderId(string $sender_id)
    {
        $this->transporters['senderId'] = $sender_id;
    }

    private function validateConfigurations()
    {
        if (!isset($this->transporters['senderId'])) {
        }
        if (!isset($this->transporters['transporters'])) {
        }
    }
}
