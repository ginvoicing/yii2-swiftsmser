<?php
/**
 * Created by PhpStorm.
 * User: tarunjangra
 * Date: 01/02/2021
 * Time: 06:38
 */

namespace yii\swiftsmser\transporter;

use yii\swiftsmser\enum\Status;
use yii\swiftsmser\enum\Type;
use yii\swiftsmser\exceptions\BalanceException;
use yii\swiftsmser\exceptions\SendException;
use yii\swiftsmser\exceptions\TimeLimitException;
use yii\swiftsmser\Response;
use yii\swiftsmser\ResponseInterface;
use yii\swiftsmser\SMSPacket;
use yii\swiftsmser\TransporterInterface;

//Promotional gateway
class ICloudMessage extends Base implements TransporterInterface
{
    private $_baseApi = 'http://msg.icloudsms.com/rest/services/sendSMS/';
    public $apiKey;

    public function getBalance(): int
    {
        $rawResponse = $this->_curl
            ->reset()
            ->get($this->_baseApi . 'getClientRouteBalance?AUTH_KEY=' . $this->apiKey);
        $decodedResponse = json_decode($rawResponse, true);
        if ($rawResponse == null) {
            throw new BalanceException('{"status":"FAILED","message": "Connection problem with the gateway.","output": null}');
        }
        // You will get responseCode parameter only if there
        // is some error in the response of the API.
        if (count($decodedResponse) && !isset($decodedResponse['responseCode'])) {
            foreach ($decodedResponse as $key => $routes) {
                //Which route you want use for sending sms enter routeId for particular route.use given Id for route.
                // 1 = Transactional Route, 2 = Promotional Route,
                // 3 = Trans DND Route, 7 = Transcrub Route, 8 = OTP Route,
                // 9 = Trans Stock Route, 10 = Trans Property Route, 11 = Trans DND Other Route,
                // 12 = TransCrub Stock, 13 = TransCrub Property, 14 = Trans Crub Route.
                if (isset($routes['displayRouteId']) && $routes['displayRouteId'] == 3) {
                    return (int)$routes['routeBalance'];
                }
            }
            throw new BalanceException('{"status":"FAILED","message": "Bad balance response","output": "' . $rawResponse . '"}');
        }
        throw new BalanceException('{"status":"FAILED","message": "Bad balance response","output": "' . $rawResponse . '"}');
    }

    public function send(SMSPacket &$packet, array $to = []): ResponseInterface
    {
        $body = $packet->getBody();
        $data = [
            'AUTH_KEY' => $this->apiKey,
            'message' => $body,
            'senderId' => $this->_senderId,
            'routeId' => 3,
            'mobileNos' => implode(',', $to),
            'entityid' => $packet->getEntityId(),
            'templateid' => $packet->getTemplateId()
        ];
        $json_encode = json_encode($data);
        if (strlen($body) != strlen(utf8_decode($body))) {
            $data['smsContentType'] = 'Unicode';
        } else {
            $data['smsContentType'] = 'English';
        }
        $rawResponse = $this->_curl
            ->reset()
            ->get($this->_baseApi . 'sendGroupSms?' . http_build_query($data));

        $responseObject = new Response();
        if ($rawResponse == null) {
            throw new SendException('{"status":"FAILED","message": "Connection problem.","input":"' . $json_encode . '","output": null}');
        }
        $responseObject->setRaw($rawResponse);
        if ($responseObject->getDecoded()->responseCode == 3001) {
            return $responseObject->setStatus(Status::SUCCESS())
                ->setResponseId($responseObject->getDecoded()->response);
        } elseif ($responseObject->getDecoded()->responseCode == 3114) {
            throw new SendException('{"status":"FAILED","message": "Promotional messages can not be sent between 9:00 PM to 9:05:05 AM","input":"' . $json_encode . '","output": null}');
        } else {
            throw new SendException('{"status":"FAILED","message": "Bad response.","input":"' . $json_encode . '","output": "' . $responseObject->getRaw() . '"}');
        }
    }
}
