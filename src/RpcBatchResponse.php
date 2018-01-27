<?php declare(strict_types=1);


namespace Terah\JsonRpc;

use function Terah\Assert\Assert;
use ArrayObject;

class RpcBatchResponse extends ArrayObject implements \JsonSerializable
{
    /**
     * @param RpcResponse $value
     * @return RpcBatchResponse
     */
    public function append($value) : RpcBatchResponse
    {
        Assert($value)->isInstanceOf(RpcResponse::class);
        parent::append($value);

        return $this;
    }

    /**
     * @return RpcResponse[]|RpcResponse
     */
    public function jsonSerialize()
    {
        if ( $this->count() === 1 )
        {
            $responses   = $this->getArrayCopy();

            return $responses[0];
        }

        return $this->getArrayCopy();
    }
}
