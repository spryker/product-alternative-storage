<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\ProductAlternativeStorage\Dependency\Client;

class ProductAlternativeStorageToStorageClientBridge implements ProductAlternativeStorageToStorageClientInterface
{
    /**
     * @var \Spryker\Client\Storage\StorageClientInterface
     */
    protected $storageClient;

    /**
     * @param \Spryker\Client\Storage\StorageClientInterface $storageClient
     */
    public function __construct($storageClient)
    {
        $this->storageClient = $storageClient;
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function get(string $key)
    {
        return $this->storageClient->get($key);
    }

    /**
     * @param array<string> $keys
     *
     * @return array<string, mixed>
     */
    public function getMulti(array $keys): array
    {
        return $this->storageClient->getMulti($keys);
    }
}
