<?php  declare(strict_types=1);

namespace Terah\JsonRpc;

class RpcFieldError
{
    /** @var string */
    protected $name     = '';

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
     * @param string[] $messages
     */
    public function setMessages(array $messages) : void
    {
        $this->messages = $messages;
    }

    /** @var string[] */
    protected $messages = [];


}
