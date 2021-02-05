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
        if ($rawResponse==null) {
            throw new BalanceException('{"status":"FAILED","message": "Connection problem with the gateway.","output": null}');
        }
        $decodedResponse = json_decode($rawResponse, true);

        if ($decodedResponse['status'] === 200 && $decodedResponse['message'] === 'OK' && isset($decodedResponse['data'])) {
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


    public function send(SMSPacket $packet, array $to = []): ResponseInterface
    {
        $data = [
            'service' => 'T',
            'template_id' => $packet->getTemplateId(),
            'variables' => $packet->getVariables(),
            'to' => implode(',', $to)
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

    public function sendold(SMSPacket $smsObject, array $to = []): ResponseInterface
    {
        $data = [
            'service' => 'T',
            'to' => implode(',', $to),
            'sender' => $this->_senderId,
            'message' => $smsObject->getBody(),
            'entity_id' => $smsObject->getEntityId(),
            'header_id' => $smsObject->getHeaderId(),
            'template_id' => $smsObject->getTemplateId()
        ];
        if (strlen($smsObject->getBody()) != strlen(utf8_decode($smsObject->getBody()))) {
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
        if ($rawResponse==null) {
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
