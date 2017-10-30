<?php declare(strict_types=1);

namespace Terah\JsonRpc;

use function Terah\Assert\Assert;

class RpcRequest implements \JsonSerializable
{
    /** @var string  */
    protected $jsonrpc  = '2.0';

    /** @var string  */
    protected $method   = '';

    /** @var mixed  */
    protected $params   = null;

    /** @var mixed */
    protected $id       = null;

    /**
     * RpcRequest constructor.
     *
     * @param \stdClass|null $data
     */
    public function __construct(\stdClass $data=null)
    {
        if ( ! $data )
        {
            return ;
        }
        Assert($data)
            ->code(-32700)
            ->isObject('Parse error: Invalid JSON was received by the server.');

        Assert($data)
            ->code(-32600)
            ->propertiesExist(['jsonrpc', 'method', 'params','id'], 'Invalid Request: The JSON sent is not a valid Request object.');

        $this
            ->setJsonrpc($data->jsonrpc)
            ->setId($data->id)
            ->setMethod($data->method)
            ->setParams($data->params);
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param $id
     * @return RpcRequest
     */
    public function setId($id) : RpcRequest
    {

        Assert($id)
            ->code(RpcError::ERROR_INVALID_REQUEST)
            ->notNull('The ID not be null')
            ->notEmpty('The ID not be empty');

        $this->id = $id;

        return $this;
    }

    /**
     * @param string $jsonrpc
     * @return RpcRequest
     */
    public function setJsonrpc(string $jsonrpc) : RpcRequest
    {
        Assert($jsonrpc)
            ->code(RpcError::ERROR_INVALID_REQUEST)
            ->eq('2.0', 'The jsonrpc version must be 2.0');

        $this->jsonrpc = $jsonrpc;

        return $this;
    }

    /**
     * @return string
     */
    public function getJsonrpc() : string
    {
        Assert($this->jsonrpc)
            ->code(RpcError::ERROR_INVALID_REQUEST)
            ->eq('2.0', 'The jsonrpc version must be 2.0');

        return $this->jsonrpc;
    }

    /**
     * @param string $method
     * @return RpcRequest
     */
    public function setMethod(string $method) : RpcRequest
    {
        Assert($method)
            ->code(RpcError::ERROR_INVALID_REQUEST)
            ->notEmpty('The method must not be empty');

        $this->method = $method;

        return $this;
    }

    /**
     * @return string
     */
    public function getMethod() : string
    {
        Assert($this->method)
            ->code(RpcError::ERROR_INVALID_REQUEST)
            ->notEmpty('The method must not be empty');

        return $this->method;
    }

    /**
     * @param $params
     * @return RpcRequest
     */
    public function setParams($params) : RpcRequest
    {
        $this->params = $params;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getParams()
    {
        return $this->params;
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
            'jsonrpc'       => $this->getJsonrpc(),
            'method'        => $this->getMethod(),
            'params'        => $this->getParams(),
            'id'            => $this->getId(),
        ];
    }
}