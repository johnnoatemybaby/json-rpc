<?php declare(strict_types=1);

namespace Terah\JsonRpc;

use function Terah\Assert\Assert;
use Terah\Assert\AssertionFailedException;

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
    const ERROR_INVALID_CREDENTIALS = -32001; //    Unauthorised
    //const ERROR_INVALID_JSON    = -32000 to -32099	Server error	Reserved for implementation-defined server-errors.

    static protected $messages      = [
        self::ERROR_INVALID_JSON        => 'Parse error.',      // Invalid JSON was received by the server. An error occurred on the server while parsing the JSON text.
        self::ERROR_INVALID_REQUEST     => 'Invalid Request.',  // The JSON sent is not a valid Request object.
        self::ERROR_METHOD_NOT_FOUND    => 'Method not found.', // The method does not exist / is not available.
        self::ERROR_INVALID_PARAMS      => 'Invalid params.',   // Invalid method parameter(s).
        self::ERROR_INTERNAL_RPC_ERROR  => 'Internal error.',   // Internal JSON-RPC error.
        self::ERROR_UNAUTHORISED        => 'Unauthorised.',     // Method not allowed
        self::ERROR_INVALID_CREDENTIALS => 'Invalid username or password.',     // Invalid username or password.
        // -32000 to -32099	Server error	Reserved for implementation-defined server-errors.
     ];

    static protected $httpCodes     = [
        self::ERROR_INVALID_JSON        => 400, // The server cannot or will not process the request due to an apparent client error (e.g., malformed request syntax, size too large, invalid request message framing, or deceptive request routing)
        self::ERROR_INVALID_REQUEST     => 400, // The server cannot or will not process the request due to an apparent client error (e.g., malformed request syntax, size too large, invalid request message framing, or deceptive request routing)
        self::ERROR_METHOD_NOT_FOUND    => 501, // The server either does not recognize the request method, or it lacks the ability to fulfil the request. Usually this implies future availability (e.g., a new feature of a web-service API).
        self::ERROR_INVALID_PARAMS      => 400, // The server cannot or will not process the request due to an apparent client error (e.g., malformed request syntax, size too large, invalid request message framing, or deceptive request routing)
        self::ERROR_INTERNAL_RPC_ERROR  => 500, // A generic error message, given when an unexpected condition was encountered and no more specific message is suitable.
        self::ERROR_UNAUTHORISED        => 403, // The request was valid, but the server is refusing action. The user might not have the necessary permissions for a resource, or may need an account of some sort.
        self::ERROR_INVALID_CREDENTIALS => 401, // Specifically for use when authentication is required and has failed or has not yet been provided.
        // -32000 to -32099	Server error	// A generic error message, given when an unexpected condition was encountered and no more specific message is suitable.
    ];

    /**
     * RpcError constructor.
     * @param \Exception|null $e
     */
    public function __construct(\Exception $e=null)
    {
        $this->data = new RpcFieldErrorCollection();
        if ( $e )
        {
            $this->setCode((int)$e->getCode());
            $this->setMessage($e->getMessage());
            if ( $e->getCode() === self::ERROR_INVALID_PARAMS )
            {
                $this->setMessage(static::$messages[self::ERROR_INVALID_PARAMS]);
            }
            if ( $e instanceof AssertionFailedException )
            {
                $name       = $e->getProperty();
                $message    = $e->getMessage();
                if ( $name && $name !== 'General Error' && $message )
                {
                    $this->appendData(new RpcFieldError($name, [$message]));
                }
            }
            if ( $e instanceof RpcException )
            {
                $this->setData($e->getData());
            }
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
     * @param bool $setDefaultMessage
     * @return RpcError
     */
    public function setCode(int $code, bool $setDefaultMessage=false) : RpcError
    {
        $this->code = $code;
        if ( $setDefaultMessage && isset(static::$messages[$code]) )
        {
            $this->setMessage(static::$messages[$code]);
        }

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
     * @return RpcError
     */
    public function setData(RpcFieldErrorCollection $data) : RpcError
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @param RpcFieldError $error
     * @return RpcError
     */
    public function appendData(RpcFieldError $error) : RpcError
    {
        $this->data->setFieldError($error);

        return $this;
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

    /**
     * @return int
     * @throws AssertionFailedException
     */
    public function getHttpStatusCode() : int
    {
        Assert($this->code)
            ->int('The error code must be an integer')
            ->notEq(0, 'The error code must not be zero');

        return static::$httpCodes[$this->code] ?? 500;
    }
}
