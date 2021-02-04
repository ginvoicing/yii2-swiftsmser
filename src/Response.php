<?php
/**
 * Created by PhpStorm.
 * User: tarunjangra
 * Date: 02/02/2021
 * Time: 22:36
 */

namespace yii\swiftsmser;

use yii\swiftsmser\enum\Status;

class Response implements ResponseInterface
{
    /** @var string */
    private $_raw;
    /** @var string */
    private $_status;
    /** @var object */
    private $_decoded;
    /** @var string */
    private $_responseId;

    public function __construct()
    {
        $this
            ->setStatus(Status::PENDING())
            ->setRaw('{"status":"ERROR", "message":"Connectivity problem."}');
    }

    public function getResponseId(): string
    {
        return $this->_responseId;
    }

    public function setResponseId(string $resp_id): ResponseInterface
    {
        $this->_responseId = $resp_id;
        return $this;
    }

    public function getDecoded(): object
    {
        return $this->_decoded;
    }

    public function getRaw(): string
    {
        return $this->_raw;
    }

    public function setRaw(string $json_response): ResponseInterface
    {
        $this->_raw = $json_response;
        $this->_decoded = json_decode($json_response);
        return $this;
    }

    public function setStatus(string $status): ResponseInterface
    {
        $this->_status = $status;
        return $this;
    }

    public function getStatus(): int
    {
        return $this->_status;
    }
}
