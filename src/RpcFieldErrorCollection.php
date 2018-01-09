<?php  declare(strict_types=1);

namespace Terah\JsonRpc;

class RpcFieldErrorCollection
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
     */
    public function setFieldErrors(array $fieldErrors)
    {
        $this->fieldErrors = $fieldErrors;
    }

    /**
     * @param RpcFieldError $fieldError
     */
    public function setFieldError(RpcFieldError $fieldError)
    {
        $this->fieldErrors[] = $fieldError;
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
