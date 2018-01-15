<?php declare(strict_types=1);

namespace Terah\JsonRpc;

use function Terah\Assert\Assert;

/**
 * Class RpcResponse
 *
 * @package Terah\JsonRpc
 */
class RpcResponse implements \JsonSerializable
{
    /** @var string  */
    protected $jsonrpc      = '2.0';

    /** @var mixed  */
    protected $result       = null;

    /** @var RpcError  */
    protected $error        = null;

    /** @var int */
    protected $id           = null;

    /**
     * RpcResponse constructor.
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
            ->propertiesExist(['jsonrpc'], 'Invalid Request: The JSON sent is not a valid Request object.');

        $this->setJsonrpc($data->jsonrpc)->setId($data->id ?? null);

        if ( isset($data->error) )
        {
            $rpcError = new RpcError;
            $rpcError->setCode($data->error->code ?? 0);
            $rpcError->setMessage($data->error->message ?? '');
            $this->setError($rpcError);
        }
        if ( isset($data->result) )
        {
            $this->setResult($data->result);
        }
    }

    /**
     * @return string
     */
    public function getJsonrpc() : string
    {
        Assert($this->jsonrpc)
            ->eq('2.0', 'The jsonrpc version must be 2.0');

        return $this->jsonrpc;
    }

    /**
     * @param string $jsonrpc
     * @return RpcResponse
     */
    public function setJsonrpc(string $jsonrpc) : RpcResponse
    {
        Assert($jsonrpc)
            ->eq('2.0', 'The jsonrpc version must be 2.0');

        $this->jsonrpc = $jsonrpc;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @param $result
     * @return RpcResponse
     */
    public function setResult($result) : RpcResponse
    {
        $this->result = $result;

        return $this;
    }

    /**
     * @return RpcError
     */
    public function getError() : RpcError
    {
        return $this->error;
    }

    /**
     * @return bool
     */
    public function hasError() : bool
    {
        return ! is_null($this->error);
    }

    /**
     * @param RpcError $error
     * @return RpcResponse
     */
    public function setError(RpcError $error) : RpcResponse
    {
        $this->error = $error;

        return $this;
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
     * @return RpcResponse
     */
    public function setId($id) : RpcResponse
    {
        Assert($id)
            ->name("id")
            ->code(RpcError::ERROR_INVALID_REQUEST);

        $this->id = $id;

        return $this;
    }

    /**
     * @return object
     */
    public function jsonSerialize()
    {
        $result             =  new \stdClass();
        $result->jsonrpc    = $this->getJsonrpc();
        $result->result     = $this->getResult();
        if ( $this->error )
        {
            $result->error  = $this->getError();
        }
        if ( $this->id )
        {
            $result->id     = $this->getId();
        }

        return $result;
    }
}