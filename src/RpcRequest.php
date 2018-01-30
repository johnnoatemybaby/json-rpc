<?php declare(strict_types=1);

namespace Terah\JsonRpc;

use function Terah\Assert\Assert;
use stdClass;

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
     * @param stdClass|null $data
     */
    public function __construct(stdClass $data=null)
    {
        if ( ! $data )
        {
            return ;
        }
        Assert($data)
            ->code(RpcError::ERROR_INVALID_JSON)
            ->isObject('Parse error: Invalid JSON was received by the server.');

        Assert($data)
            ->code(RpcError::ERROR_INVALID_REQUEST)
            ->propertiesExist(['jsonrpc', 'method', 'params', 'id'], 'Invalid Request: The JSON sent is not a valid Request object.');

        $this
            ->setJsonrpc($data->jsonrpc)
            ->setId($data->id)
            ->setMethod($data->method)
            ->setParams($data->params);
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return RpcRequest
     */
    public function setId($id) : RpcRequest
    {
        $this->requestAssert($id)->notEmpty('The ID not be empty');

        $this->id = $id;

        return $this;
    }

    /**
     * @param string $jsonrpc
     * @return RpcRequest
     */
    public function setJsonrpc(string $jsonrpc) : RpcRequest
    {
        $this->requestAssert($jsonrpc)->eq('2.0', 'The jsonrpc version must be 2.0');

        $this->jsonrpc = $jsonrpc;

        return $this;
    }

    /**
     * @return string
     */
    public function getJsonrpc() : string
    {
        $this->requestAssert($this->jsonrpc)->eq('2.0', 'The jsonrpc version must be 2.0');

        return $this->jsonrpc;
    }

    /**
     * @param string $method
     * @return RpcRequest
     */
    public function setMethod(string $method) : RpcRequest
    {
        $this->requestAssert($method)->notEmpty('The method must not be empty');

        $this->method = $method;

        return $this;
    }

    /**
     * @return string
     */
    public function getMethod() : string
    {
        $this->requestAssert($this->method)->notEmpty('The method must not be empty');

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
     * @return stdClass
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param string $name
     * @return string
     */
    public function getParamStr(string $name) : string
    {
        $this->paramAssert($name)->scalar("The parameter '{$name}' is not a scalar value.");

        return (string)$this->params->{$name};
    }

    /**
     * @param string $name
     * @return int
     */
    public function getParamInt(string $name) : int
    {
        $this->paramAssert($name)->numeric("The parameter '{$name}' is not a numeric value.");

        return (int)$this->params->{$name};
    }

    /**
     * @param string $name
     * @return int
     */
    public function getParamId(string $name) : int
    {
        $id = $this->getParamInt($name);
        $this->paramAssert($name)->value($id)->id("The parameter '{$name}' is not an ID.");

        return $id;
    }

    /**
     * @param string $name
     * @return float
     */
    public function getParamFloat(string $name) : float
    {
        $this->paramAssert($name)->scalar("The parameter '{$name}' is not a scalar value.");

        return (float)$this->params->{$name};
    }

    /**
     * @param string $name
     * @return stdClass
     */
    public function getParamObj(string $name) : stdClass
    {
        $this->paramAssert($name)->isObject("The parameter '{$name}' is not an object.");

        return (object)$this->params->{$name};
    }

    /**
     * @param string $name
     * @return array
     */
    public function getParamArr(string $name) : array
    {
        $this->paramAssert($name)->isObject("The parameter '{$name}' is not an object.");

        return (array)$this->params->{$name};
    }

    /**
     * @param string $name
     * @return bool
     */
    public function getParamBool(string $name) : bool
    {
        $this->paramAssert($name)->scalar("The parameter '{$name}' is not a scalar value.");

        return in_array($this->params->{$name}, ['1', 1, 'Yes', true, 'true']);
    }

    /**
     * @param string $name
     * @return \Terah\Assert\Assert
     */
    protected function paramAssert(string $name) : \Terah\Assert\Assert
    {
        Assert($this->params)
            ->name($name)
            ->code(RpcError::ERROR_INVALID_PARAMS)
            ->propertyExists($name, "The parameter '{$name}' was not specified.");

        return Assert($this->params->{$name})
            ->name($name)
            ->code(RpcError::ERROR_INVALID_PARAMS);
    }

    /**
     * @param mixed $value
     * @return \Terah\Assert\Assert
     */
    protected function requestAssert($value) : \Terah\Assert\Assert
    {
        return Assert($value)->code(RpcError::ERROR_INVALID_REQUEST);
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
