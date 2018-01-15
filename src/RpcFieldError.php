<?php  declare(strict_types=1);

namespace Terah\JsonRpc;

class RpcFieldError implements \JsonSerializable
{
    /** @var string */
    protected $name     = '';

    /** @var string[] */
    protected $messages = [];

    /**
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name) : void
    {
        $this->name = $name;
    }

    /**
     * @return string[]
     */
    public function getMessages() : array
    {
        return $this->messages;
    }

    /**
     * @param string $message
     */
    public function setMessage(string $message) : void
    {
        $this->messages[] = $message;
    }

    /**
     * @param string[] $messages
     */
    public function setMessages(array $messages) : void
    {
        $this->messages = $messages;
    }

    /**
     * @return object
     */
    public function jsonSerialize()
    {
        return (object)$this->toArray();
    }

    /**
     * @return array
     */
    public function toArray() : array
    {
        return [
            'name'          => $this->getName(),
            'messages'      => $this->getMessages()
        ];
    }

}
