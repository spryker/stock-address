<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\StockAddress\Business\Updater;

use Generated\Shared\Transfer\StockAddressTransfer;
use Generated\Shared\Transfer\StockResponseTransfer;
use Generated\Shared\Transfer\StockTransfer;
use Spryker\Zed\Kernel\Persistence\EntityManager\TransactionTrait;
use Spryker\Zed\StockAddress\Business\Deleter\StockAddressDeleterInterface;
use Spryker\Zed\StockAddress\Persistence\StockAddressEntityManagerInterface;
use Spryker\Zed\StockAddress\Persistence\StockAddressRepositoryInterface;

class StockAddressUpdater implements StockAddressUpdaterInterface
{
    use TransactionTrait;

    /**
     * @var \Spryker\Zed\StockAddress\Persistence\StockAddressEntityManagerInterface
     */
    protected $stockAddressEntityManager;

    /**
     * @var \Spryker\Zed\StockAddress\Persistence\StockAddressRepositoryInterface
     */
    protected $stockAddressRepository;

    /**
     * @var \Spryker\Zed\StockAddress\Business\Deleter\StockAddressDeleterInterface
     */
    protected $stockAddressDeleter;

    public function __construct(
        StockAddressEntityManagerInterface $stockAddressEntityManager,
        StockAddressRepositoryInterface $stockAddressRepository,
        StockAddressDeleterInterface $stockAddressDeleter
    ) {
        $this->stockAddressEntityManager = $stockAddressEntityManager;
        $this->stockAddressRepository = $stockAddressRepository;
        $this->stockAddressDeleter = $stockAddressDeleter;
    }

    public function updateStockAddressForStock(StockTransfer $stockTransfer): StockResponseTransfer
    {
        if ($stockTransfer->getAddress()) {
            return $this->getTransactionHandler()->handleTransaction(function () use ($stockTransfer) {
                return $this->executeUpdateStockAddressForStockTransaction($stockTransfer);
            });
        }

        if ($this->stockAddressRepository->isStockAddressExistsForStock($stockTransfer->getIdStockOrFail())) {
            return $this->stockAddressDeleter->deleteStockAddressForStock($stockTransfer);
        }

        return (new StockResponseTransfer())
            ->setStock($stockTransfer)
            ->setIsSuccessful(true);
    }

    protected function executeUpdateStockAddressForStockTransaction(StockTransfer $stockTransfer): StockResponseTransfer
    {
        $stockAddressTransfer = $stockTransfer->getAddressOrFail();
        $this->assertStockAddressTransfer($stockAddressTransfer);

        $stockAddressTransfer->setIdStock($stockTransfer->getIdStockOrFail());
        $stockAddressTransfer = $this->stockAddressEntityManager->saveStockAddress($stockAddressTransfer);

        $stockTransfer->setAddress($stockAddressTransfer);

        return (new StockResponseTransfer())
            ->setStock($stockTransfer)
            ->setIsSuccessful(true);
    }

    protected function assertStockAddressTransfer(StockAddressTransfer $stockAddressTransfer): void
    {
        $stockAddressTransfer
            ->requireAddress1()
            ->requireCity()
            ->requireZipCode()
            ->getCountryOrFail()
            ->requireIdCountry();
    }
}
