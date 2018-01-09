<?php declare(strict_types=1);


namespace Terah\JsonRpc;

use function Terah\Assert\Assert;
use ArrayObject;

class RpcBatchResponse extends ArrayObject implements \JsonSerializable
{
    /**
     * @param RpcResponse $value
     */
    public function append($value)
    {
        Assert($value)->isInstanceOf(RpcResponse::class);
        parent::append($value);
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
