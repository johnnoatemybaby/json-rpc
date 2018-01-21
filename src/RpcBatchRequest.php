<?php declare(strict_types=1);


namespace Terah\JsonRpc;


use function Terah\Assert\Assert;
use ArrayObject;

class RpcBatchRequest extends ArrayObject implements \JsonSerializable
{
    /**
     * RpcBatchRequest constructor.
     *
     * @param array|\stdClass $data
     */
    public function __construct($data=[])
    {
        Assert($data)
            ->code(RpcError::ERROR_INVALID_JSON)
            ->notEmpty('Parse error: Invalid JSON was received by the server.');

        $this->setFlags(ArrayObject::STD_PROP_LIST|ArrayObject::ARRAY_AS_PROPS);
        $data       = is_array($data) ? $data : [$data];

        foreach ( $data as $request )
        {
            $this->append(new RpcRequest($request));
        }
    }

    /**
     * @param RpcRequest $value
     */
    public function append($value)
    {
        Assert($value)->isInstanceOf(RpcRequest::class);
        parent::append($value);
    }

    /**
     * @return RpcRequest[]|RpcRequest
     */
    public function jsonSerialize()
    {
        if ( $this->count() === 1 )
        {
            return $this->offsetGet(0);
        }

        return $this->getArrayCopy();
    }
}
