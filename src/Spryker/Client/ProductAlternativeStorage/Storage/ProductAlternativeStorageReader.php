<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\ProductAlternativeStorage\Storage;

use Generated\Shared\Transfer\ProductAlternativeStorageTransfer;
use Generated\Shared\Transfer\SynchronizationDataTransfer;
use Spryker\Client\ProductAlternativeStorage\Dependency\Client\ProductAlternativeStorageToStorageClientInterface;
use Spryker\Client\ProductAlternativeStorage\Dependency\Service\ProductAlternativeStorageToSynchronizationServiceInterface;
use Spryker\Shared\ProductAlternativeStorage\ProductAlternativeStorageConfig;

class ProductAlternativeStorageReader implements ProductAlternativeStorageReaderInterface
{
    /**
     * @var \Spryker\Client\ProductAlternativeStorage\Dependency\Client\ProductAlternativeStorageToStorageClientInterface
     */
    protected $storageClient;

    /**
     * @var \Spryker\Client\ProductAlternativeStorage\Dependency\Service\ProductAlternativeStorageToSynchronizationServiceInterface
     */
    protected $synchronizationService;

    public function __construct(
        ProductAlternativeStorageToStorageClientInterface $storageClient,
        ProductAlternativeStorageToSynchronizationServiceInterface $synchronizationService
    ) {
        $this->storageClient = $storageClient;
        $this->synchronizationService = $synchronizationService;
    }

    public function findProductAlternativeStorage(string $concreteSku): ?ProductAlternativeStorageTransfer
    {
        $key = $this->generateKey($concreteSku);
        $productAlternativeStorageData = $this->storageClient->get($key);

        if (!$productAlternativeStorageData) {
            return null;
        }

        return $this->mapToProductAlternativeStorage($productAlternativeStorageData);
    }

    protected function mapToProductAlternativeStorage(array $productAlternativeStorageData): ProductAlternativeStorageTransfer
    {
        return (new ProductAlternativeStorageTransfer())
            ->fromArray($productAlternativeStorageData, true);
    }

    protected function generateKey(string $concreteSku): string
    {
        $synchronizationDataTransfer = (new SynchronizationDataTransfer())
            ->setReference($concreteSku);

        return $this->synchronizationService
            ->getStorageKeyBuilder(ProductAlternativeStorageConfig::PRODUCT_ALTERNATIVE_RESOURCE_NAME)
            ->generateKey($synchronizationDataTransfer);
    }
}
