<?php
/**
 * Created by PhpStorm.
 * User: tarunjangra
 * Date: 01/02/2021
 * Time: 23:40
 */

namespace yii\swiftsmser;

use yii\base\BaseObject;

class SMSPacket extends BaseObject
{
    public $variables = [];
    public $header = null;
    public $body = null;
    public $footer = null;
    /** @var string DLT template id */
    public $templateId;
    /** @var string DLT entity id */
    public $entityId;
    /** @var string DLT header id */
    public $headerId;
    /** @var array sms character length to count 1 sms */
    public $charLength = ['normal' => 165, 'unicode' => 65];

    /** @var array Array of phone numbers being used to send sms */
    private $_to = [];


    public function setTo(array $to): self
    {
        $this->_to = $to;
        return $this;
    }

    public function getTo(): array
    {
        return $this->_to;
    }

    public function setBody(string $body, array $vars = []): self
    {
        $this->body = $body;
        $this->variables += array_merge($this->variables, $vars);
        return $this;
    }

    public function getBody(): string
    {
        $body = $this->header . $this->body . $this->footer;
        foreach ($this->variables as $var) {
            $body = $this->replaceVars('{#var#}', $var, $body);
        }
        return $body;
    }

    public function setHeader(string $header, array $vars = []): self
    {
        $this->header = $header;
        $this->variables += array_merge($this->variables, $vars);
        return $this;
    }

    public function setFooter(string $footer, array $vars = []): self
    {
        $this->footer = $footer;
        $this->variables += array_merge($this->variables, $vars);
        return $this;
    }

    /**
     * Get number of sms to be consumed by given phone numbers
     *
     * @return int
     */
    public function getDeduction(): int
    {
        $totalCount = count($this->to) ?? 1;
        $char_length = $this->charLength['normal'];
        if (mb_detect_encoding($this->getBody(), 'ASCII', true) != 'ASCII') {
            $char_length = $this->charLength['unicode'];
        }
        return ceil(mb_strlen($this->getBody(), 'UTF-8') / $char_length) * $totalCount;
    }

    private function replaceVars($search_str, $replacement_str, $src_str)
    {
        return (false !== ($pos = strpos($src_str, $search_str))) ? substr_replace(
            $src_str,
            $replacement_str,
            $pos,
            strlen($search_str)
        ) : $src_str;
    }
}
