<?php  declare(strict_types=1);

namespace Terah\JsonRpc;

class RpcFieldErrorCollection implements \JsonSerializable
{
    /** @var RpcFieldError[] */
    protected $fieldErrors = [];

    /**
     * @return RpcFieldError[]
     */
    public function getFieldErrors(): array
    {
        return $this->fieldErrors;
    }

    /**
     * @param RpcFieldError[] $fieldErrors
     * @return RpcFieldErrorCollection
     */
    public function setFieldErrors(array $fieldErrors) : RpcFieldErrorCollection
    {
        $this->fieldErrors  = [];
        foreach ( $fieldErrors as $error )
        {
            $this->setFieldError($error);
        }

        return $this;
    }

    /**
     * @param RpcFieldError $fieldError
     * @return RpcFieldErrorCollection
     */
    public function setFieldError(RpcFieldError $fieldError) : RpcFieldErrorCollection
    {
        $this->fieldErrors[] = $fieldError;

        return $this;
    }

    /**
     * @return object
     */
    public function jsonSerialize()
    {
        $data = [];
        foreach ( $this->fieldErrors as $fieldError )
        {
            $data[$fieldError->getName()] = $fieldError->getMessages();
        }

        return (object)$data;
    }
}
