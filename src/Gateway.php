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
    /** @var array */
    private $_transporters;

    /** @var string */
    private $_senderId;

    /** @var TransporterInterface */
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
     * Get selected transporter to be used to send a SMS.
     * This property is not read only property for this component.
     *
     * @return TransporterInterface
     */
    public function getTransporter()
    {
        return $this->_transporter;
    }

    /**
     * This is read-write property to be used in the component configurations.
     * You can get all transporters available in the configurations.
     *
     * @return array Array of transporters
     */
    public function getTransporters(): array
    {
        return $this->_transporters;
    }

    /**
     * Set transporters from the configurations of the component.
     *
     * @param array $transporters
     */
    public function setTransporters(array $transporters): void
    {
        $this->_transporters = $transporters;
    }

    /**
     * Get sender Id configured in component configurations.
     *
     * @return string
     */
    public function getSenderId(): string
    {
        return $this->_senderId;
    }

    /**
     * Set sender id from the configuration of the component.
     *
     * @param string $sender_id
     */

    public function setSenderId(string $sender_id)
    {
        $this->_senderId = $sender_id;
    }

    /**
     * Returns the Component instance for promotional transporters.
     *
     * @return $this
     * @throws BadGatewayException
     * @throws TransporterNotFoundException
     */

    public function getPromotional(): self
    {
        $this->_transporter = $this->getGateway(Type::PROMOTIONAL());
        return $this;
    }

    /**
     * Returns the component instance for the transactional transporters.
     *
     * @return $this
     * @throws BadGatewayException
     * @throws TransporterNotFoundException
     */

    public function getTransactional(): self
    {
        $this->_transporter = $this->getGateway(Type::TRANSACTIONAL());
        return $this;
    }

    /**
     * Send sms to given number and returns the response instance.
     *
     * @param SMSPacket $packet
     * @param array $to
     * @return ResponseInterface
     * @throws exceptions\SendException
     */

    public function send(SMSPacket $packet, array $to = []): ResponseInterface
    {
        return $this->_transporter->send($packet, $to);
    }

    /**
     * Get credit balance of SMS available in the account.
     *
     * @return int
     * @throws exceptions\BalanceException
     */
    public function getBalance(): int
    {
        return $this->_transporter->getBalance();
    }

    /**
     * Select random transporter from the array of transporters of the given type.
     *
     * @param Type $type Promotional or Transactional types.
     * @return object Return instance of the Transporter.
     * @throws BadGatewayException
     * @throws InvalidConfigException
     * @throws TransporterNotFoundException
     */
    private function getGateway(Type $type): object
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
