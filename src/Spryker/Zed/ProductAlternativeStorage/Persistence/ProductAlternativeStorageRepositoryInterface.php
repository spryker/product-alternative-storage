<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductAlternativeStorage\Persistence;

use Generated\Shared\Transfer\FilterTransfer;
use Orm\Zed\ProductAlternativeStorage\Persistence\SpyProductReplacementForStorage;

interface ProductAlternativeStorageRepositoryInterface
{
    /**
     * @param array<int> $productIds
     *
     * @return array<\Orm\Zed\ProductAlternativeStorage\Persistence\SpyProductAlternativeStorage>
     */
    public function findProductAlternativeStorageEntities(array $productIds): array;

    /**
     * @module Product
     *
     * @param int $idProduct
     *
     * @return string
     */
    public function findProductSkuById($idProduct): string;

    /**
     * @module ProductAlternative
     *
     * @param int $idProduct
     *
     * @return array<int>
     */
    public function findAbstractAlternativesIdsByConcreteProductId($idProduct): array;

    /**
     * @module ProductAlternative
     *
     * @param int $idProduct
     *
     * @return array<int>
     */
    public function findConcreteAlternativesIdsByConcreteProductId($idProduct): array;

    /**
     * @module Product
     *
     * @param array<int> $productIds
     *
     * @return array<string>
     */
    public function getIndexedProductConcreteIdToSkusByProductIds(array $productIds): array;

    /**
     * @module Product
     *
     * @param array<int> $productIds
     *
     * @return array<string>
     */
    public function getIndexedProductAbstractIdToSkusByProductIds(array $productIds): array;

    /**
     * @module Product
     *
     * @param array<int> $productIds
     *
     * @return array<string>
     */
    public function getIndexedProductConcreteIdToSkusByProductAbstractIds(array $productIds): array;

    /**
     * @param string $sku
     *
     * @return \Orm\Zed\ProductAlternativeStorage\Persistence\SpyProductReplacementForStorage|null
     */
    public function findProductReplacementStorageEntitiesBySku(string $sku): ?SpyProductReplacementForStorage;

    /**
     * @module ProductAlternative
     *
     * @param int $idProductAbstract
     *
     * @return array<int>
     */
    public function getReplacementsByAbstractProductId(int $idProductAbstract): array;

    /**
     * @module ProductAlternative
     *
     * @param int $idProductConcrete
     *
     * @return array<int>
     */
    public function getReplacementsByConcreteProductId(int $idProductConcrete): array;

    /**
     * @deprecated Use {@link getSynchronizationDataTransfersByFilterAndProductAlternativeStorageIds()} instead.
     *
     * @see \Spryker\Zed\ProductAlternativeStorage\Persistence\ProductAlternativeStorageRepositoryInterface::getSynchronizationDataTransfersByFilterAndProductAlternativeStorageIds()
     *
     * @return array<\Orm\Zed\ProductAlternativeStorage\Persistence\SpyProductAlternativeStorage>
     */
    public function findAllProductAlternativeStorageEntities(): array;

    /**
     * @deprecated Use {@link getSynchronizationDataTransfersByFilterAndProductAlternativeStorageIds()} instead.
     *
     * @see \Spryker\Zed\ProductAlternativeStorage\Persistence\ProductAlternativeStorageRepositoryInterface::getSynchronizationDataTransfersByFilterAndProductAlternativeStorageIds()
     *
     * @param array<int> $productAlternativeStorageIds
     *
     * @return array<\Orm\Zed\ProductAlternativeStorage\Persistence\SpyProductAlternativeStorage>
     */
    public function findProductAlternativeStorageEntitiesByIds(array $productAlternativeStorageIds): array;

    /**
     * @deprecated Use {@link getSynchronizationDataTransfersByFilterAndProductReplacementForStorageIds()} instead.
     *
     * @see \Spryker\Zed\ProductAlternativeStorage\Persistence\ProductAlternativeStorageRepositoryInterface::getSynchronizationDataTransfersByFilterAndProductReplacementForStorageIds()
     *
     * @return array<\Orm\Zed\ProductAlternativeStorage\Persistence\SpyProductReplacementForStorage>
     */
    public function findAllProductReplacementForStorageEntities(): array;

    /**
     * @deprecated Use {@link getSynchronizationDataTransfersByFilterAndProductReplacementForStorageIds()} instead.
     *
     * @see \Spryker\Zed\ProductAlternativeStorage\Persistence\ProductAlternativeStorageRepositoryInterface::getSynchronizationDataTransfersByFilterAndProductReplacementForStorageIds()
     *
     * @param array<int> $productReplacementForStorageIds
     *
     * @return array<\Orm\Zed\ProductAlternativeStorage\Persistence\SpyProductReplacementForStorage>
     */
    public function findProductReplacementForStorageEntitiesByIds(array $productReplacementForStorageIds): array;

    /**
     * @param \Generated\Shared\Transfer\FilterTransfer $filterTransfer
     * @param array<int> $productAlternativeStorageIds
     *
     * @return array<\Generated\Shared\Transfer\SynchronizationDataTransfer>
     */
    public function getSynchronizationDataTransfersByFilterAndProductAlternativeStorageIds(
        FilterTransfer $filterTransfer,
        array $productAlternativeStorageIds = []
    ): array;

    /**
     * @param \Generated\Shared\Transfer\FilterTransfer $filterTransfer
     * @param array<int> $productReplacementForStorageIds
     *
     * @return array<\Generated\Shared\Transfer\SynchronizationDataTransfer>
     */
    public function getSynchronizationDataTransfersByFilterAndProductReplacementForStorageIds(
        FilterTransfer $filterTransfer,
        array $productReplacementForStorageIds = []
    ): array;
}
