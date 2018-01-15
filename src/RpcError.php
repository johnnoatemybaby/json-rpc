<?php declare(strict_types=1);

namespace Terah\JsonRpc;

use function Terah\Assert\Assert;

class RpcError implements \JsonSerializable
{
    /** @var int  */
    protected $code                 = 0;

    /** @var string  */
    protected $message              = '';

    /** @var RpcFieldErrorCollection */
    protected $data                 = null;

    const ERROR_INVALID_JSON        = -32700; //    Parse error	Invalid JSON was received by the server.  An error occurred on the server while parsing the JSON text.
    const ERROR_INVALID_REQUEST     = -32600; //	Invalid Request	The JSON sent is not a valid Request object.
    const ERROR_METHOD_NOT_FOUND    = -32601; //	Method not found	The method does not exist / is not available.
    const ERROR_INVALID_PARAMS      = -32602; //	Invalid params	Invalid method parameter(s).
    const ERROR_INTERNAL_RPC_ERROR  = -32603; //	Internal error	Internal JSON-RPC error.
    const ERROR_UNAUTHORISED        = -32000; //    Unauthorised
    //const ERROR_INVALID_JSON    = -32000 to -32099	Server error	Reserved for implementation-defined server-errors.

    /**
     * RpcError constructor.
     * @param \Exception|null $e
     */
    public function __construct(\Exception $e=null)
    {
        if ( $e )
        {
            $this->setCode($e->getCode());
            $this->setMessage($e->getMessage());
        }
    }

    /**
     * @return RpcError
     */
    public function setMethodNotExistsError() : RpcError
    {
        return $this->setCode(self::ERROR_METHOD_NOT_FOUND)->setMessage('The method does not exist');
    }

    /**
     * @return int
     */
    public function getCode() : int
    {
        return $this->code;
    }

    /**
     * @param int $code
     * @return RpcError
     */
    public function setCode(int $code) : RpcError
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return string
     */
    public function getMessage() : string
    {
        return $this->message;
    }

    /**
     * @param string $message
     * @return RpcError
     */
    public function setMessage(string $message) : RpcError
    {
        $this->message = $message;

        return $this;
    }

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

    /**
     * @return object
     */
    public function jsonSerialize()
    {
        $this->validate();

        return (object)[
            'code'          => $this->getCode(),
            'message'       => $this->getMessage(),
            'data'          => $this->getData(),
        ];
    }

    /**
     * @return bool
     */
    public function validate() : bool
    {
        Assert($this->code)
            ->int('The error code must be an integer')
            ->notEq(0, 'The error code must not be zero');

        Assert($this->message)
            ->notEmpty('The error must not be empty');

        return true;
    }



}
