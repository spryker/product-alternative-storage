<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductAlternativeStorage\Communication\ProductAlternativeStorageMapper;

use Generated\Shared\Transfer\SynchronizationDataTransfer;

/**
 * @deprecated Will be removed without replacement.
 */
class ProductAlternativeStorageMapper implements ProductAlternativeStorageMapperInterface
{
    /**
     * @param array<\Orm\Zed\ProductAlternativeStorage\Persistence\SpyProductAlternativeStorage> $productAlternativeStorageEntities
     *
     * @return array<\Generated\Shared\Transfer\SynchronizationDataTransfer>
     */
    public function mapProductAlternativeStorageEntitiesToSynchronizationDataTransfers(array $productAlternativeStorageEntities): array
    {
        $synchronizationDataTransfers = [];

        foreach ($productAlternativeStorageEntities as $productAlternativeStorageEntity) {
            $synchronizationDataTransfer = new SynchronizationDataTransfer();
            /** @var string $data */
            $data = $productAlternativeStorageEntity->getData();
            $synchronizationDataTransfer->setData($data);
            $synchronizationDataTransfer->setKey($productAlternativeStorageEntity->getKey());
            $synchronizationDataTransfers[] = $synchronizationDataTransfer;
        }

        return $synchronizationDataTransfers;
    }
}
