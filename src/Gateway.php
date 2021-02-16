<?php
/**
 * Created by PhpStorm.
 * User: tarunjangra
 * Date: 01/02/2021
 * Time: 09:55
 */

namespace yii\swiftsmser;

use linslin\yii2\curl\Curl;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\swiftsmser\enum\Status;
use yii\swiftsmser\enum\Type;
use yii\swiftsmser\exceptions\BadGatewayException;
use yii\swiftsmser\exceptions\SendException;
use yii\swiftsmser\exceptions\TransporterNotFoundException;
use yii\swiftsmser\logging\Logger;
use yii\swiftsmser\logging\LoggerInterface;

class Gateway extends Component
{
    /** @var array Pass available transporters. */
    public $transporters = null;

    /** @var string A sender id which will be used to send SMS */
    public $senderId = null;

    /** @var bool If you want to enable logging success and failure of the SMSs. logging = ['connection' => 'db'] */
    public $logging = false;

    /** @var array Character length of unicode and normal sms to be deducted from SMS Credit. */
    public $charLength = ['unicode' => 65, 'normal' => 165];

    /** @var string DLT headerId allocated from https://vilpower.in */
    public $headerId;

    /** @var string DLT entityId allocated from https://vilpower.in */
    public $entityId;

    /** @var string Footer for all sms being sent from the gateway */
    public $footer;


    /**
     * @var LoggerInterface|null
     */
    private $_logger = null;

    /** @var TransporterInterface */
    private $_transporter;

    public function init()
    {
        parent::init(); // TODO: Change the autogenerated stub

        if (!$this->transporters) {
            throw new InvalidConfigException('Property "transporters" is mandatory for swiftsmser component.');
        }
        if (!$this->senderId) {
            throw new InvalidConfigException('Property "senderId" is mandatory for swiftsmser component.');
        }

        if (!$this->headerId) {
            throw new InvalidConfigException('Property "headerId" is mandatory for swiftsmser component. Get from vilpower.in');
        }

        if (!$this->entityId) {
            throw new InvalidConfigException('Property "entityId" is mandatory for swiftsmser component. Get from vilpower.in');
        }

        if (isset($this->charLength['normal']) && isset($this->charLength['unicode']) && !$this->charLength['unicode'] && !$this->charLength['normal']) {
            throw new InvalidConfigException('Property "charLength" should be an array having non zero integers ["normal" => 65,"unicode"=>165].');
        }

        if ($this->logging && $this->_logger === null) {
            if (!isset($this->logging['connection']) || empty($this->logging['connection']) ||
                (is_array($this->logging['connection']) && count($this->logging['connection']) === 0)
            ) {
                throw new InvalidConfigException('Logging connection must be set.');
            }
            if (!isset($this->logging['class']) || empty($this->logging['class'])) {
                $this->logging['class'] = Logger::class;
            }
            $this->_logger = \Yii::createObject($this->logging);
        }
        $this->footer = "\ngnvc.in/a";
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
     * Returns the Component instance for promotional transporters.
     *
     * @return $this
     * @throws BadGatewayException
     * @throws InvalidConfigException
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
     * @throws InvalidConfigException
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
     * @return ResponseInterface | false
     * @throws exceptions\SendException
     */

    public function send(SMSPacket &$packet): ResponseInterface
    {
        $packet->headerId = $this->headerId;
        $packet->entityId = $this->entityId;
        $packet->setFooter($this->footer);
        if ($this->charLength) {
            $packet->charLength = $this->charLength;
        }
        try {
            $response = $this->getTransporter()->send($packet);
            if ($this->_logger instanceof LoggerInterface && $response->getStatus() == Status::SUCCESS()) {
                $this->_logger->setRecord([
                    'response_id' => $response->getResponseId(),
                    'phone' => implode(',', $packet->to),
                    'message' => $packet->getBody(),
                    'type' => $this->getTransporter()->type,
                    'deduction' => $packet->deduction,
                    'status' => $response->getStatus(),
                    'transporter' => get_class($this->getTransporter()),
                    'raw' => $response->getRaw()
                ]);
            }
            return $response;
        } catch (SendException $e) {
            if ($this->_logger instanceof LoggerInterface) {
                $this->_logger->setRecord([
                    'response_id' => null,
                    'phone' => implode(',', $packet->to),
                    'message' => $packet->getBody(),
                    'type' => $this->getTransporter()->type,
                    'deduction' => 0,
                    'status' => Status::FAILED(),
                    'transporter' => get_class($this->getTransporter()),
                    'raw' => $e->getMessage()
                ]);
            }
            // forward the same exception
            throw new SendException($e->getMessage());
        }
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
     * @return bool|LoggerInterface|null
     */

    public function getLogger()
    {
        if ($this->_logger instanceof LoggerInterface) {
            return $this->_logger;
        }

        return false;
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
