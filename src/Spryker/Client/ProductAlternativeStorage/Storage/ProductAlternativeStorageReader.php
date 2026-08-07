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
use Spryker\Service\UtilEncoding\UtilEncodingServiceInterface;
use Spryker\Shared\ProductAlternativeStorage\ProductAlternativeStorageConfig;

class ProductAlternativeStorageReader implements ProductAlternativeStorageReaderInterface
{
    /**
     * @uses \Spryker\Zed\Storage\Communication\Table\StorageTable::KV_PREFIX
     *
     * @var string
     */
    protected const KV_PREFIX = 'kv:';

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
        ProductAlternativeStorageToSynchronizationServiceInterface $synchronizationService,
        protected UtilEncodingServiceInterface $utilEncodingService
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

    /**
     * @param array<string> $concreteSkus
     *
     * @return array<string, \Generated\Shared\Transfer\ProductAlternativeStorageTransfer>
     */
    public function getProductAlternativeStorages(array $concreteSkus): array
    {
        $storageKeysByConcreteSku = $this->generateKeys($concreteSkus);
        $productAlternativeStorageDataCollection = $this->storageClient->getMulti(array_values($storageKeysByConcreteSku));

        $productAlternativeStorageTransfers = [];

        foreach ($storageKeysByConcreteSku as $concreteSku => $storageKey) {
            $productAlternativeStorageData = $productAlternativeStorageDataCollection[static::KV_PREFIX . $storageKey] ?? null;

            if (!$productAlternativeStorageData) {
                continue;
            }

            $productAlternativeStorageTransfers[$concreteSku] = $this->mapToProductAlternativeStorage(
                (array)$this->utilEncodingService->decodeJson($productAlternativeStorageData, true),
            );
        }

        return $productAlternativeStorageTransfers;
    }

    /**
     * @param array<string> $concreteSkus
     *
     * @return array<string, string>
     */
    protected function generateKeys(array $concreteSkus): array
    {
        $storageKeysByConcreteSku = [];

        foreach ($concreteSkus as $concreteSku) {
            $storageKeysByConcreteSku[$concreteSku] = $this->generateKey($concreteSku);
        }

        return $storageKeysByConcreteSku;
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
