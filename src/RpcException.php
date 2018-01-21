<?php declare(strict_types=1);


namespace Terah\JsonRpc;


class RpcException extends \Exception
{
    /** @var RpcFieldErrorCollection */
    protected $data = null;

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param RpcFieldErrorCollection $data
     */
    public function setData(RpcFieldErrorCollection $data)
    {
        $this->data = $data;
    }
}
