<?php
/**
 * Created by PhpStorm.
 * User: tarunjangra
 * Date: 01/02/2021
 * Time: 23:40
 */

namespace yii\swiftsmser;

class SMSPacket
{
    private $_header;
    private $_body;
    private $_footer;
    private $_variables = [];

    /** @var string DLT template id */
    private $_templateId;
    /** @var string DLT entity id */
    private $_entityId;
    /** @var string DLT header id */
    private $_headerId;

    public function getEntityId(): string
    {
        return $this->_entityId;
    }

    public function getVariables(): array
    {
        return $this->_variables;
    }

    public function setVariables(array $vars = []): self
    {
        $this->_variables = $vars;
        return $this;
    }

    public function setEntityId(string $entity_id): self
    {
        $this->_entityId = $entity_id;
        return $this;
    }

    public function getHeaderId(): string
    {
        return $this->_headerId;
    }

    public function setHeaderId(string $header_id): self
    {
        $this->_headerId = $header_id;
        return $this;
    }

    public function getTemplateId(): string
    {
        return $this->_templateId;
    }

    public function setTemplateID(string $template_id): self
    {
        $this->_templateId = $template_id;
        return $this;
    }

    public function setBody(string $body, array $vars = []): self
    {
        $this->_body = $body;
        $this->_variables += array_merge($this->_variables, $vars);
        return $this;
    }

    public function getBody(): string
    {
        $body = $this->_header . $this->_body . $this->_footer;
        foreach ($this->_variables as &$var) {
            $body = preg_replace('/\{#var#\}/', $var, $body, 1);
        }
        return $body;
    }

    public function setHeader(string $header, array $vars = []): self
    {
        $this->_header = $header;
        $this->_variables += array_merge($this->_variables, $vars);
        return $this;
    }

    public function setFooter(string $footer, array $vars = []): self
    {
        $this->_footer = $footer;
        $this->_variables += array_merge($this->_variables, $vars);
        return $this;
    }
}
