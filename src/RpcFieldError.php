<?php  declare(strict_types=1);

namespace Terah\JsonRpc;

class RpcFieldError implements \JsonSerializable
{
    /** @var string */
    protected $name         = '';

    /** @var string[] */
    protected $messages     = [];

    /**
     * RpcFieldError constructor.
     *
     * @param string $name
     * @param string[]  $messages
     */
    public function __construct(string $name, array $messages)
    {
        $this->setName($name);
        $this->setMessages($messages);
    }

    /**
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return RpcFieldError
     */
    public function setName(string $name) : RpcFieldError
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getMessages() : array
    {
        return $this->messages;
    }

    /**
     * @param array $messages
     * @return RpcFieldError
     */
    public function setMessages(array $messages) : RpcFieldError
    {
        $this->messages     = [];
        foreach ( $messages as $message )
        {
            $this->appendMessage($message);
        }

        return $this;
    }

    /**
     * @param string $message
     * @return RpcFieldError
     */
    public function appendMessage(string $message) : RpcFieldError
    {
        $this->messages[] = $message;

        return $this;
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
