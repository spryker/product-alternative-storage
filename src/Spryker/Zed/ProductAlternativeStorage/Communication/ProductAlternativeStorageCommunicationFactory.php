<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductAlternativeStorage\Communication;

use Spryker\Zed\Kernel\Communication\AbstractCommunicationFactory;
use Spryker\Zed\ProductAlternativeStorage\Communication\ProductAlternativeStorageMapper\ProductAlternativeStorageMapper;
use Spryker\Zed\ProductAlternativeStorage\Communication\ProductAlternativeStorageMapper\ProductAlternativeStorageMapperInterface;
use Spryker\Zed\ProductAlternativeStorage\Communication\ProductAlternativeStorageMapper\ProductReplacementForStorageMapper;
use Spryker\Zed\ProductAlternativeStorage\Communication\ProductAlternativeStorageMapper\ProductReplacementForStorageMapperInterface;
use Spryker\Zed\ProductAlternativeStorage\Dependency\Facade\ProductAlternativeStorageToEventBehaviorFacadeInterface;
use Spryker\Zed\ProductAlternativeStorage\Dependency\Facade\ProductAlternativeStorageToProductAlternativeFacadeInterface;
use Spryker\Zed\ProductAlternativeStorage\ProductAlternativeStorageDependencyProvider;

/**
 * @method \Spryker\Zed\ProductAlternativeStorage\Business\ProductAlternativeStorageFacadeInterface getFacade()
 * @method \Spryker\Zed\ProductAlternativeStorage\ProductAlternativeStorageConfig getConfig()
 * @method \Spryker\Zed\ProductAlternativeStorage\Persistence\ProductAlternativeStorageEntityManagerInterface getEntityManager()
 * @method \Spryker\Zed\ProductAlternativeStorage\Persistence\ProductAlternativeStorageRepositoryInterface getRepository()
 */
class ProductAlternativeStorageCommunicationFactory extends AbstractCommunicationFactory
{
    public function getEventBehaviorFacade(): ProductAlternativeStorageToEventBehaviorFacadeInterface
    {
        return $this->getProvidedDependency(ProductAlternativeStorageDependencyProvider::FACADE_EVENT_BEHAVIOR);
    }

    public function createProductAlternativeStorageMapper(): ProductAlternativeStorageMapperInterface
    {
        return new ProductAlternativeStorageMapper();
    }

    public function createProductReplacementForStorageMapper(): ProductReplacementForStorageMapperInterface
    {
        return new ProductReplacementForStorageMapper();
    }

    public function getProductAlternativeFacade(): ProductAlternativeStorageToProductAlternativeFacadeInterface
    {
        return $this->getProvidedDependency(ProductAlternativeStorageDependencyProvider::FACADE_PRODUCT_ALTERNATIVE);
    }
}
