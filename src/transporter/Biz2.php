<?php

/**
 * Created by PhpStorm.
 * User: tarunjangra
 * Date: 01/02/2021
 * Time: 06:38
 */

namespace yii\swiftsmser\transporter;

use yii\swiftsmser\enum\Status;
use yii\swiftsmser\exceptions\BalanceException;
use yii\swiftsmser\exceptions\SendException;
use yii\swiftsmser\Response;
use yii\swiftsmser\ResponseInterface;
use yii\swiftsmser\SMSPacket;
use yii\swiftsmser\TransporterInterface;

// Transactional gateway
class Biz2 extends Base implements TransporterInterface
{
    private $_baseApi = 'http://biz2.smslounge.in/api/v2/';
    public $apiKey;

    public function getBalance(): int
    {
        $rawResponse = $this->_curl
            ->reset()
            ->setHeaders(['Authorization' => "Bearer {$this->apiKey}"])
            ->post($this->_baseApi . 'account/balance');
        if ($rawResponse == null) {
            throw new BalanceException('{"status":"FAILED","message": "Connection problem with the gateway.","output": null}');
        }
        $decodedResponse = json_decode($rawResponse, true);

        if (isset($decodedResponse['status']) && $decodedResponse['status'] === 'OK' &&
        isset($decodedResponse['message']) && $decodedResponse['message'] === 'OK' &&
        isset($decodedResponse['data'])) {
            foreach ($decodedResponse['data'] as $data) {
                // This promotional gateway is sending message
                // through Transactional channel.
                if ($data['service'] === 'T') {
                    return (int)$data['credits'];
                }
            }
            throw new BalanceException('{"status":"FAILED","message": "Bad balance response","output": "' . $rawResponse . '"}');
        }
        throw new BalanceException('{"status":"FAILED","message": "Bad balance response","output": "' . $rawResponse . '"}');
    }

    public function send(SMSPacket &$packet): ResponseInterface
    {
        return $this->sendWithNormalAPI($packet);
    }

    private function sendWithTemplateApi(SMSPacket $packet): ResponseInterface
    {
        $data = [
            'service' => 'T',
            'template_id' => $packet->templateId,
            'variables' => $packet->variables,
            'to' => implode($this->_delimiter, $packet->to)
        ];

        $json_encode = json_encode($data);

        $rawResponse = $this->_curl
            ->reset()
            ->setOption(CURLOPT_POSTFIELDS, $json_encode)
            ->setHeaders(['Content-Type' => 'application/json'])
            ->post($this->_baseApi . 'sms/template?access_token=' . $this->apiKey);
        $responseObject = new Response();
        if ($rawResponse == null) {
            throw new SendException('{"status":"FAILED","message": "Connection problem.","input":"' . $json_encode . '","output": null}');
        }
        $responseObject->setRaw($rawResponse);
        if ($responseObject->getDecoded()->status == 200 && count($responseObject->getDecoded()->data) === 1) {
            return $responseObject->setStatus(Status::SUCCESS())
                ->setResponseId($responseObject->getDecoded()->data[0]->id);
        } else {
            throw new SendException('{"status":"FAILED","message": "Bad response.","input":"' . $json_encode . '","output": "' . $responseObject->getRaw() . '"}');
        }
    }

    private function sendWithNormalAPI(SMSPacket $packet): ResponseInterface
    {
        $data = [
            'root' =>  [
                'sender' => $this->_senderId,
                'service' => 'T',
                'entity_id' => $packet->entityId,
                'header_id' => $packet->headerId,
                'template_id' => $packet->templateId
            ],
            'nodes' => [
                'to' => implode(',', $packet->to),
                'message' => $packet->getBody()
            ]
        ];
        if (strlen($packet->getBody()) != strlen(utf8_decode($packet->getBody()))) {
            $data['type'] = 'U';
        } else {
            $data['type'] = 'N';
        }
        $json_encode = json_encode($data);
        $rawResponse = $this->_curl
            ->reset()
            ->setOption(CURLOPT_POSTFIELDS, $json_encode)
            ->setHeaders(['Content-Type' => 'application/json'])
            ->setHeaders(['Authorization' => "Bearer {$this->apiKey}"])
            ->post($this->_baseApi . 'sms/send/json?');
        $responseObject = new Response();
        if ($rawResponse == null) {
            throw new SendException('{"status":"FAILED","message": "Connection problem.","input":"' . $json_encode . '","output": null}');
        }
        $responseObject->setRaw($rawResponse);
        if ($responseObject->getDecoded()->status == 200) {
            return $responseObject->setStatus(Status::SUCCESS())
                ->setResponseId($responseObject->getDecoded()->data[0]->id);
        } else {
            throw new SendException('{"status":"FAILED","message": "Bad response.","input":"' . $json_encode . '","output": "' . $responseObject->getRaw() . '"}');
        }
    }
}
