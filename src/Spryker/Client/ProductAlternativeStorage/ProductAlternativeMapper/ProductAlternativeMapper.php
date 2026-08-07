<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\ProductAlternativeStorage\ProductAlternativeMapper;

use ArrayObject;
use Generated\Shared\Transfer\ConcreteAlternativeProductCollectionTransfer;
use Generated\Shared\Transfer\ConcreteAlternativeProductCriteriaTransfer;
use Generated\Shared\Transfer\ConcreteAlternativeProductTransfer;
use Generated\Shared\Transfer\ProductAlternativeStorageTransfer;
use Generated\Shared\Transfer\ProductViewTransfer;
use Spryker\Client\ProductAlternativeStorage\Dependency\Client\ProductAlternativeStorageToProductStorageClientInterface;
use Spryker\Client\ProductAlternativeStorage\ProductAlternativeStorageConfig;
use Spryker\Client\ProductAlternativeStorage\Storage\ProductAlternativeStorageReaderInterface;

class ProductAlternativeMapper implements ProductAlternativeMapperInterface
{
    /**
     * @var string
     */
    protected const PRODUCT_CONCRETE_IDS = 'product_concrete_ids';

    /**
     * @var \Spryker\Client\ProductAlternativeStorage\Dependency\Client\ProductAlternativeStorageToProductStorageClientInterface
     */
    protected $productStorageClient;

    /**
     * @var \Spryker\Client\ProductAlternativeStorage\Storage\ProductAlternativeStorageReaderInterface
     */
    protected $productAlternativeStorageReader;

    public function __construct(
        ProductAlternativeStorageReaderInterface $productAlternativeStorageReader,
        ProductAlternativeStorageToProductStorageClientInterface $productStorageClient
    ) {
        $this->productAlternativeStorageReader = $productAlternativeStorageReader;
        $this->productStorageClient = $productStorageClient;
    }

    /**
     * @param \Generated\Shared\Transfer\ProductViewTransfer $productViewTransfer
     * @param string $localeName
     *
     * @return array<\Generated\Shared\Transfer\ProductViewTransfer>
     */
    public function getConcreteAlternativeProducts(ProductViewTransfer $productViewTransfer, string $localeName): array
    {
        $productReplacementForStorage = $this->productAlternativeStorageReader
            ->findProductAlternativeStorage($productViewTransfer->getSku());
        if (!$productReplacementForStorage) {
            return [];
        }

        $productViewTransferList = [];
        $productConcreteIds = $productReplacementForStorage->getProductConcreteIds();
        $productConcreteIds = array_merge(
            $productConcreteIds,
            $this->findConcreteProductIdsByAbstractProductIds(
                $productReplacementForStorage->getProductAbstractIds(),
                $localeName,
            ),
        );
        foreach ($productConcreteIds as $idProduct) {
            $concreteProductViewTransfer = $this->findConcreteProductViewTransfer($idProduct, $localeName);

            if (!$concreteProductViewTransfer) {
                continue;
            }

            $productViewTransfer = clone $productViewTransfer;
            $productViewTransferList[] = $productViewTransfer->fromArray($concreteProductViewTransfer->modifiedToArray());
        }

        /** @phpstan-ignore arrayFilter.same */
        return array_filter($productViewTransferList);
    }

    public function getConcreteAlternativeProductCollection(
        ConcreteAlternativeProductCriteriaTransfer $concreteAlternativeProductCriteriaTransfer
    ): ConcreteAlternativeProductCollectionTransfer {
        $concreteAlternativeProductConditionsTransfer = $concreteAlternativeProductCriteriaTransfer
            ->getConcreteAlternativeProductConditionsOrFail();
        $localeName = $concreteAlternativeProductConditionsTransfer->getLocaleNameOrFail();

        $concreteAlternativeProductCollectionTransfer = new ConcreteAlternativeProductCollectionTransfer();
        $productAlternativeStorageTransfers = $this->productAlternativeStorageReader
            ->getProductAlternativeStorages($concreteAlternativeProductConditionsTransfer->getSkus());

        if (!$productAlternativeStorageTransfers) {
            return $concreteAlternativeProductCollectionTransfer;
        }

        $productConcreteIdsBySku = $this->getAlternativeProductConcreteIdsBySku($productAlternativeStorageTransfers, $localeName);
        $concreteProductViewTransfers = $this->getConcreteProductViewTransfersIndexedByIdProductConcrete(
            array_merge([], ...array_values($productConcreteIdsBySku)),
            $localeName,
        );

        foreach ($productConcreteIdsBySku as $sku => $productConcreteIds) {
            $alternativeProductViewTransfers = $this->getAlternativeProductViewTransfers($productConcreteIds, $concreteProductViewTransfers);

            if (!$alternativeProductViewTransfers) {
                continue;
            }

            $concreteAlternativeProductCollectionTransfer->addConcreteAlternativeProduct(
                (new ConcreteAlternativeProductTransfer())
                    ->setSku((string)$sku)
                    ->setAlternativeProducts(new ArrayObject($alternativeProductViewTransfers)),
            );
        }

        return $concreteAlternativeProductCollectionTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\ProductViewTransfer $productViewTransfer
     * @param string $localeName
     *
     * @return array<\Generated\Shared\Transfer\ProductViewTransfer>
     */
    public function getAlternativeProducts(ProductViewTransfer $productViewTransfer, string $localeName): array
    {
        if ($productViewTransfer->getIdProductConcrete()) {
            return $this->getAlternativeProductsByConcreteProductSku($productViewTransfer->getSku(), $localeName);
        }

        return $this->getAlternativeProductsByAbstractProductSku($productViewTransfer, $localeName);
    }

    /**
     * @param string $concreteSku
     * @param string $localeName
     *
     * @return array<\Generated\Shared\Transfer\ProductViewTransfer>
     */
    protected function getAlternativeProductsByConcreteProductSku(string $concreteSku, string $localeName): array
    {
        $productAlternativeStorage = $this->productAlternativeStorageReader->findProductAlternativeStorage($concreteSku);
        if (!$productAlternativeStorage) {
            return [];
        }

        return $this->mapProductAlternativeStorageToProductView($productAlternativeStorage, $localeName);
    }

    /**
     * @param \Generated\Shared\Transfer\ProductViewTransfer $productViewTransfer
     * @param string $localeName
     *
     * @return array<\Generated\Shared\Transfer\ProductViewTransfer>
     */
    protected function getAlternativeProductsByAbstractProductSku(ProductViewTransfer $productViewTransfer, string $localeName): array
    {
        $productAlternativeStorage = $this->findProductAlternativeStorageForAbstractProduct($productViewTransfer);
        if (!$productAlternativeStorage) {
            return [];
        }

        return $this->mapProductAlternativeStorageToProductView($productAlternativeStorage, $localeName);
    }

    /**
     * @param \Generated\Shared\Transfer\ProductAlternativeStorageTransfer $productAlternativeStorage
     * @param string $localeName
     *
     * @return array<\Generated\Shared\Transfer\ProductViewTransfer>
     */
    protected function mapProductAlternativeStorageToProductView(
        ProductAlternativeStorageTransfer $productAlternativeStorage,
        string $localeName
    ): array {
        $productViewTransferList = [];
        foreach ($productAlternativeStorage->getProductAbstractIds() as $idProductAbstract) {
            $productViewTransferList[] = $this->findAbstractProductViewTransfer($idProductAbstract, $localeName);
        }

        foreach ($productAlternativeStorage->getProductConcreteIds() as $idProduct) {
            $productViewTransferList[] = $this->findConcreteProductViewTransfer($idProduct, $localeName);
        }

        return array_filter($productViewTransferList);
    }

    protected function findProductAlternativeStorageForAbstractProduct(ProductViewTransfer $productViewTransfer): ?ProductAlternativeStorageTransfer
    {
        $attributeMap = $productViewTransfer->getAttributeMap();
        if (!$attributeMap) {
            return null;
        }
        $productAlternativeStorageTransfer = new ProductAlternativeStorageTransfer();
        $productAbstractIds = [];
        $productConcreteIds = [];
        foreach (array_keys($attributeMap->getProductConcreteIds()) as $concreteSku) {
            $concreteProductAlternativeStorageTransfer = $this->productAlternativeStorageReader->findProductAlternativeStorage($concreteSku);
            if (!$concreteProductAlternativeStorageTransfer) {
                return null;
            }
            $productAbstractIds = array_merge($productAbstractIds, $concreteProductAlternativeStorageTransfer->getProductAbstractIds());
            $productConcreteIds = array_merge($productConcreteIds, $concreteProductAlternativeStorageTransfer->getProductConcreteIds());
        }

        return $productAlternativeStorageTransfer
            ->setProductAbstractIds(array_unique($productAbstractIds))
            ->setProductConcreteIds(array_unique($productConcreteIds));
    }

    protected function findConcreteProductViewTransfer(int $idProduct, string $localeName): ?ProductViewTransfer
    {
        $productViewTransfer = $this->productStorageClient
            ->findProductConcreteViewTransfer($idProduct, $localeName);

        if ($productViewTransfer && $productViewTransfer->getAvailable()) {
            return $productViewTransfer;
        }

        return null;
    }

    protected function findAbstractProductViewTransfer(int $idProductAbstract, string $localeName): ?ProductViewTransfer
    {
        return $this->productStorageClient->findProductAbstractViewTransfer($idProductAbstract, $localeName);
    }

    /**
     * @param array<string, \Generated\Shared\Transfer\ProductAlternativeStorageTransfer> $productAlternativeStorageTransfers
     * @param string $localeName
     *
     * @return array<string, array<int>>
     */
    protected function getAlternativeProductConcreteIdsBySku(array $productAlternativeStorageTransfers, string $localeName): array
    {
        $productAbstractIds = [];

        foreach ($productAlternativeStorageTransfers as $productAlternativeStorageTransfer) {
            $productAbstractIds[] = $productAlternativeStorageTransfer->getProductAbstractIds();
        }

        $productConcreteIdsByIdProductAbstract = $this->getConcreteProductIdsGroupedByIdProductAbstract(
            array_unique(array_merge([], ...$productAbstractIds)),
            $localeName,
        );

        $productConcreteIdsBySku = [];

        foreach ($productAlternativeStorageTransfers as $sku => $productAlternativeStorageTransfer) {
            $productConcreteIds = $productAlternativeStorageTransfer->getProductConcreteIds();

            foreach ($productAlternativeStorageTransfer->getProductAbstractIds() as $idProductAbstract) {
                $productConcreteIds = array_merge(
                    $productConcreteIds,
                    $productConcreteIdsByIdProductAbstract[$idProductAbstract] ?? [],
                );
            }

            $productConcreteIdsBySku[$sku] = array_values(array_unique($productConcreteIds));
        }

        return $productConcreteIdsBySku;
    }

    /**
     * @param array<int> $productAbstractIds
     * @param string $localeName
     *
     * @return array<int, array<int>>
     */
    protected function getConcreteProductIdsGroupedByIdProductAbstract(array $productAbstractIds, string $localeName): array
    {
        if (!$productAbstractIds) {
            return [];
        }

        $productAbstractStorageDataCollection = $this
            ->productStorageClient
            ->getBulkProductAbstractStorageDataByProductAbstractIdsAndLocaleName(array_values($productAbstractIds), $localeName);

        $productConcreteIdsByIdProductAbstract = [];

        foreach ($productAbstractStorageDataCollection as $idProductAbstract => $productAbstractStorageData) {
            $productConcreteIdsByIdProductAbstract[$idProductAbstract] = array_values(
                $productAbstractStorageData[ProductAlternativeStorageConfig::RESOURCE_TYPE_ATTRIBUTE_MAP][static::PRODUCT_CONCRETE_IDS] ?? [],
            );
        }

        return $productConcreteIdsByIdProductAbstract;
    }

    /**
     * @param array<int> $productConcreteIds
     * @param string $localeName
     *
     * @return array<int, \Generated\Shared\Transfer\ProductViewTransfer> Keys are concrete product IDs.
     */
    protected function getConcreteProductViewTransfersIndexedByIdProductConcrete(array $productConcreteIds, string $localeName): array
    {
        $productConcreteIds = array_values(array_unique($productConcreteIds));

        if (!$productConcreteIds) {
            return [];
        }

        $concreteProductViewTransfers = [];

        foreach ($this->productStorageClient->getProductConcreteViewTransfers($productConcreteIds, $localeName) as $concreteProductViewTransfer) {
            $idProductConcrete = $concreteProductViewTransfer->getIdProductConcrete();

            if ($idProductConcrete === null || !$concreteProductViewTransfer->getAvailable()) {
                continue;
            }

            $concreteProductViewTransfers[$idProductConcrete] = $concreteProductViewTransfer;
        }

        return $concreteProductViewTransfers;
    }

    /**
     * @param array<int> $productConcreteIds
     * @param array<int, \Generated\Shared\Transfer\ProductViewTransfer> $concreteProductViewTransfers
     *
     * @return array<\Generated\Shared\Transfer\ProductViewTransfer>
     */
    protected function getAlternativeProductViewTransfers(array $productConcreteIds, array $concreteProductViewTransfers): array
    {
        $alternativeProductViewTransfers = [];

        foreach ($productConcreteIds as $idProductConcrete) {
            if (!isset($concreteProductViewTransfers[$idProductConcrete])) {
                continue;
            }

            $alternativeProductViewTransfers[] = $concreteProductViewTransfers[$idProductConcrete];
        }

        return $alternativeProductViewTransfers;
    }

    /**
     * @param array<int> $abstractProductIds
     * @param string $localeName
     *
     * @return array<int>
     */
    protected function findConcreteProductIdsByAbstractProductIds(array $abstractProductIds, string $localeName): array
    {
        $productConcreteIds = [];
        $productAbstractStorageDataCollection = $this
            ->productStorageClient
            ->getBulkProductAbstractStorageDataByProductAbstractIdsAndLocaleName($abstractProductIds, $localeName);

        foreach ($productAbstractStorageDataCollection as $productAbstractStorageData) {
            $productConcreteIds = array_merge(
                $productConcreteIds,
                $productAbstractStorageData[ProductAlternativeStorageConfig::RESOURCE_TYPE_ATTRIBUTE_MAP][static::PRODUCT_CONCRETE_IDS] ?? [],
            );
        }

        return array_values($productConcreteIds);
    }
}
