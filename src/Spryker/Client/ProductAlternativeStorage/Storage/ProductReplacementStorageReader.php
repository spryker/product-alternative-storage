<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\ProductAlternativeStorage\Storage;

use Generated\Shared\Transfer\ProductReplacementStorageTransfer;
use Generated\Shared\Transfer\SynchronizationDataTransfer;
use Spryker\Client\ProductAlternativeStorage\Dependency\Client\ProductAlternativeStorageToStorageClientInterface;
use Spryker\Client\ProductAlternativeStorage\Dependency\Service\ProductAlternativeStorageToSynchronizationServiceInterface;
use Spryker\Shared\ProductAlternativeStorage\ProductAlternativeStorageConfig;

class ProductReplacementStorageReader implements ProductReplacementStorageReaderInterface
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

    public function findProductAlternativeStorage(string $sku): ?ProductReplacementStorageTransfer
    {
        $key = $this->generateKey($sku);
        $productReplacementStorageData = $this->storageClient->get($key);

        if (!$productReplacementStorageData) {
            return null;
        }

        return $this->mapToProductAlternativeStorage($productReplacementStorageData);
    }

    protected function mapToProductAlternativeStorage(array $productReplacementStorageData): ProductReplacementStorageTransfer
    {
        return (new ProductReplacementStorageTransfer())
            ->fromArray($productReplacementStorageData, true);
    }

    protected function generateKey(string $sku): string
    {
        $synchronizationDataTransfer = (new SynchronizationDataTransfer())
            ->setReference($sku);

        return $this->synchronizationService
            ->getStorageKeyBuilder(ProductAlternativeStorageConfig::PRODUCT_REPLACEMENT_RESOURCE_NAME)
            ->generateKey($synchronizationDataTransfer);
    }
}
