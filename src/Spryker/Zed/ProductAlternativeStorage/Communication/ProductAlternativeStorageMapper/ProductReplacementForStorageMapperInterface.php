<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductAlternativeStorage\Communication\ProductAlternativeStorageMapper;

/**
 * @deprecated Will be removed without replacement.
 */
interface ProductReplacementForStorageMapperInterface
{
    /**
     * @param array<\Orm\Zed\ProductAlternativeStorage\Persistence\SpyProductReplacementForStorage> $productReplacementForStorageEntities
     *
     * @return array<\Generated\Shared\Transfer\SynchronizationDataTransfer>
     */
    public function mapProductReplacementForStorageEntitiesToSynchronizationDataTransfers(array $productReplacementForStorageEntities): array;
}
