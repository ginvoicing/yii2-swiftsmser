<?php
/**
 * Created by PhpStorm.
 * User: tarunjangra
 * Date: 01/02/2021
 * Time: 09:55
 */

namespace yii\swiftsmser;

use linslin\yii2\curl\Curl;
use MyCLabs\Enum\Enum;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\swiftsmser\enum\Type;
use yii\swiftsmser\exceptions\BadGatewayException;
use yii\swiftsmser\exceptions\TransporterNotFoundException;

class Gateway extends Component
{
    private $_transporters;
    private $_senderId;
    private $_transporter;

    public function __construct($config = [])
    {
        if (!isset($config['transporters'])) {
            throw new InvalidConfigException('Property "transporters" is mandatory for swiftsmser component.');
        }
        if (!isset($config['senderId'])) {
            throw new InvalidConfigException('Property "senderId" is mandatory for swiftsmser component.');
        }
        $this->_transporters = $config['transporters'];
        $this->_senderId = $config['senderId'];

        parent::__construct($config);
    }

    /**
     * @return mixed
     */
    public function getTransporter()
    {
        return $this->_transporter;
    }

    public function getTransporters(): array
    {
        return $this->_transporters;
    }

    public function setTransporters(array $transporters): void
    {
        $this->_transporters = $transporters;
    }

    public function getSenderId(): string
    {
        return $this->_senderId;
    }

    public function setSenderId(string $sender_id)
    {
        $this->_senderId = $sender_id;
    }

    public function getPromotional(): self
    {
        $this->_transporter = $this->getGateway(Type::PROMOTIONAL());
        return $this;
    }

    public function getTransactional(): self
    {
        $this->_transporter = $this->getGateway(Type::TRANSACTIONAL());
        return $this;
    }

    public function send(SMSPacket $packet, array $to = []): ResponseInterface
    {
        return $this->_transporter->send($packet, $to);
    }

    public function getBalance(): int
    {
        return $this->_transporter->getBalance();
    }

    private function getGateway(Type $type): TransporterInterface
    {
        $gateways = [];
        foreach ($this->transporters as $transporter) {
            if (isset($transporter['type']) && $transporter['type'] == $type) {
                $gateways[] = $transporter;
            }
        }
        if (count($gateways)) {
            $selected_transporter = $gateways[array_rand($gateways)];
            if (isset($selected_transporter['class']) && class_exists($selected_transporter['class'])) {
                // Create curl object to be passed.
                $curlObject = new Curl();
                // useragent for the gateway calls.
                $curlObject->setOption(CURLOPT_USERAGENT, 'yii-swiftsmser');

                $params = [
                    'class' => $selected_transporter['class'],
                    'type' => $type
                ];
                $params += $selected_transporter['params'] ?? [];
                return \Yii::createObject($params, [$this->senderId, $curlObject]);
            } else {
                throw new TransporterNotFoundException("Not found: \"{$selected_transporter['class']}\"");
            }
        }
        throw new BadGatewayException("SMS {$type} transporter is undefined.", 210419832);
    }
}
