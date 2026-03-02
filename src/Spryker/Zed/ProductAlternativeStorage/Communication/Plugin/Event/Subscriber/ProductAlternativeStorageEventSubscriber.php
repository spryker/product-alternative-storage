<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductAlternativeStorage\Communication\Plugin\Event\Subscriber;

use Spryker\Shared\ProductAlternativeStorage\ProductAlternativeStorageConfig;
use Spryker\Zed\Event\Dependency\EventCollectionInterface;
use Spryker\Zed\Event\Dependency\Plugin\EventSubscriberInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\ProductAlternative\Dependency\ProductAlternativeEvents;
use Spryker\Zed\ProductAlternativeStorage\Communication\Plugin\Event\Listener\ProductAbstractReplacementStorageListener;
use Spryker\Zed\ProductAlternativeStorage\Communication\Plugin\Event\Listener\ProductAlternativePublishStorageListener;
use Spryker\Zed\ProductAlternativeStorage\Communication\Plugin\Event\Listener\ProductAlternativeReplacementStorageListener;
use Spryker\Zed\ProductAlternativeStorage\Communication\Plugin\Event\Listener\ProductAlternativeStorageListener;
use Spryker\Zed\ProductAlternativeStorage\Communication\Plugin\Event\Listener\ProductConcreteReplacementStorageListener;

/**
 * @method \Spryker\Zed\ProductAlternativeStorage\Communication\ProductAlternativeStorageCommunicationFactory getFactory()
 * @method \Spryker\Zed\ProductAlternativeStorage\Business\ProductAlternativeStorageFacadeInterface getFacade()
 * @method \Spryker\Zed\ProductAlternativeStorage\ProductAlternativeStorageConfig getConfig()
 */
class ProductAlternativeStorageEventSubscriber extends AbstractPlugin implements EventSubscriberInterface
{
    /**
     * @api
     *
     * @param \Spryker\Zed\Event\Dependency\EventCollectionInterface $eventCollection
     *
     * @return \Spryker\Zed\Event\Dependency\EventCollectionInterface
     */
    public function getSubscribedEvents(EventCollectionInterface $eventCollection)
    {
        $this->addProductAlternativesListeners($eventCollection);
        $this->addProductAlternativeReplacementsListeners($eventCollection);

        return $eventCollection;
    }

    protected function addProductAlternativesListeners(EventCollectionInterface $eventCollection): void
    {
        $this->addProductAlternativePublishListener($eventCollection);
        $this->addProductAlternativeUnpublishListener($eventCollection);
        $this->addProductAlternativeCreateListener($eventCollection);
        $this->addProductAlternativeUpdateListener($eventCollection);
        $this->addProductAlternativeDeleteListener($eventCollection);
    }

    protected function addProductAlternativePublishListener(EventCollectionInterface $eventCollection): void
    {
        $eventCollection
            ->addListenerQueued(ProductAlternativeStorageConfig::PRODUCT_ALTERNATIVE_PUBLISH, new ProductAlternativePublishStorageListener(), 0, null, $this->getConfig()->getProductAlternativeEventQueueName());
    }

    protected function addProductAlternativeUnpublishListener(EventCollectionInterface $eventCollection): void
    {
        $eventCollection
            ->addListenerQueued(ProductAlternativeEvents::PRODUCT_ALTERNATIVE_UNPUBLISH, new ProductAlternativeStorageListener(), 0, null, $this->getConfig()->getProductAlternativeEventQueueName());
    }

    protected function addProductAlternativeCreateListener(EventCollectionInterface $eventCollection): void
    {
        $eventCollection
            ->addListenerQueued(ProductAlternativeEvents::ENTITY_SPY_PRODUCT_ALTERNATIVE_CREATE, new ProductAlternativeStorageListener(), 0, null, $this->getConfig()->getProductAlternativeEventQueueName());
    }

    protected function addProductAlternativeUpdateListener(EventCollectionInterface $eventCollection): void
    {
        $eventCollection
            ->addListenerQueued(ProductAlternativeEvents::ENTITY_SPY_PRODUCT_ALTERNATIVE_UPDATE, new ProductAlternativeStorageListener(), 0, null, $this->getConfig()->getProductAlternativeEventQueueName());
    }

    protected function addProductAlternativeDeleteListener(EventCollectionInterface $eventCollection): void
    {
        $eventCollection
            ->addListenerQueued(ProductAlternativeEvents::ENTITY_SPY_PRODUCT_ALTERNATIVE_DELETE, new ProductAlternativeStorageListener(), 0, null, $this->getConfig()->getProductAlternativeEventQueueName());
    }

    protected function addProductAlternativeReplacementsListeners(EventCollectionInterface $eventCollection): void
    {
        $this->addReplacementsProductAlternativePublishListener($eventCollection);
        $this->addReplacementsProductAlternativeCreateListener($eventCollection);
        $this->addReplacementsProductAlternativeUpdateListener($eventCollection);
        $this->addReplacementsProductAlternativeDeleteListener($eventCollection);

        $this->addReplacementsAbstractPublishListener($eventCollection);
        $this->addReplacementsConcretePublishListener($eventCollection);
    }

    protected function addReplacementsProductAlternativePublishListener(EventCollectionInterface $eventCollection): void
    {
        $eventCollection
            ->addListenerQueued(ProductAlternativeStorageConfig::PRODUCT_ALTERNATIVE_PUBLISH, new ProductAlternativeReplacementStorageListener(), 0, null, $this->getConfig()->getProductReplacementForEventQueueName());
    }

    protected function addReplacementsProductAlternativeCreateListener(EventCollectionInterface $eventCollection): void
    {
        $eventCollection
            ->addListenerQueued(ProductAlternativeEvents::ENTITY_SPY_PRODUCT_ALTERNATIVE_CREATE, new ProductAlternativeReplacementStorageListener(), 0, null, $this->getConfig()->getProductReplacementForEventQueueName());
    }

    protected function addReplacementsProductAlternativeUpdateListener(EventCollectionInterface $eventCollection): void
    {
        $eventCollection
            ->addListenerQueued(ProductAlternativeEvents::ENTITY_SPY_PRODUCT_ALTERNATIVE_UPDATE, new ProductAlternativeReplacementStorageListener(), 0, null, $this->getConfig()->getProductReplacementForEventQueueName());
    }

    protected function addReplacementsProductAlternativeDeleteListener(EventCollectionInterface $eventCollection): void
    {
        $eventCollection
            ->addListenerQueued(ProductAlternativeEvents::ENTITY_SPY_PRODUCT_ALTERNATIVE_DELETE, new ProductAlternativeReplacementStorageListener(), 0, null, $this->getConfig()->getProductReplacementForEventQueueName());
    }

    protected function addReplacementsAbstractPublishListener(EventCollectionInterface $eventCollection): void
    {
        $eventCollection
            ->addListenerQueued(ProductAlternativeEvents::PRODUCT_REPLACEMENT_ABSTRACT_PUBLISH, new ProductAbstractReplacementStorageListener(), 0, null, $this->getConfig()->getProductReplacementForEventQueueName());
    }

    protected function addReplacementsConcretePublishListener(EventCollectionInterface $eventCollection): void
    {
        $eventCollection
            ->addListenerQueued(ProductAlternativeEvents::PRODUCT_REPLACEMENT_CONCRETE_PUBLISH, new ProductConcreteReplacementStorageListener(), 0, null, $this->getConfig()->getProductReplacementForEventQueueName());
    }
}
