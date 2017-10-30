<?php declare(strict_types=1);


namespace Terah\JsonRpc;


interface RpcServiceInterface
{
    /**
     * @param RpcRequest $request
     * @return RpcResponse
     */
    public function handle(RpcRequest $request) : RpcResponse;

    /**
     * @param RpcBatchRequest $batch
     * @return RpcBatchResponse
     */
    public function handleBatch(RpcBatchRequest $batch) : RpcBatchResponse;
}