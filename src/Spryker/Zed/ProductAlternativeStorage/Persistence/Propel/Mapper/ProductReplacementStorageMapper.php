<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductAlternativeStorage\Persistence\Propel\Mapper;

use Generated\Shared\Transfer\SynchronizationDataTransfer;
use Orm\Zed\ProductAlternativeStorage\Persistence\SpyProductReplacementForStorage;
use Propel\Runtime\Collection\ObjectCollection;

class ProductReplacementStorageMapper
{
    /**
     * @param \Propel\Runtime\ActiveRecord\ActiveRecordInterface[]|\Propel\Runtime\Collection\ObjectCollection $productReplacementForStorageEntityCollection
     *
     * @return \Generated\Shared\Transfer\SynchronizationDataTransfer[]
     */
    public function mapProductReplacementForStorageEntityCollectionToProductReplacementForStorageTransfers(ObjectCollection $productReplacementForStorageEntityCollection): array
    {
        $synchronizationDataTransfers = [];

        foreach ($productReplacementForStorageEntityCollection as $productReplacementForStorageEntity) {
            $synchronizationDataTransfers[] = $this->mapProductReplacementForStorageEntityToSynchronizationDataTransfer(
                $productReplacementForStorageEntity,
                new SynchronizationDataTransfer()
            );
        }

        return $synchronizationDataTransfers;
    }

    /**
     * @param \Orm\Zed\ProductAlternativeStorage\Persistence\SpyProductReplacementForStorage $productReplacementForStorageEntity
     * @param \Generated\Shared\Transfer\SynchronizationDataTransfer $synchronizationDataTransfer
     *
     * @return \Generated\Shared\Transfer\SynchronizationDataTransfer
     */
    public function mapProductReplacementForStorageEntityToSynchronizationDataTransfer(
        SpyProductReplacementForStorage $productReplacementForStorageEntity,
        SynchronizationDataTransfer $synchronizationDataTransfer
    ): SynchronizationDataTransfer {
        return $synchronizationDataTransfer
            ->setData($productReplacementForStorageEntity->getData())
            ->setKey($productReplacementForStorageEntity->getKey());
    }
}
