<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\StockAddress\Business\Creator;

use Generated\Shared\Transfer\StockAddressTransfer;
use Generated\Shared\Transfer\StockResponseTransfer;
use Generated\Shared\Transfer\StockTransfer;
use Spryker\Zed\Kernel\Persistence\EntityManager\TransactionTrait;
use Spryker\Zed\StockAddress\Persistence\StockAddressEntityManagerInterface;

class StockAddressCreator implements StockAddressCreatorInterface
{
    use TransactionTrait;

    /**
     * @var \Spryker\Zed\StockAddress\Persistence\StockAddressEntityManagerInterface
     */
    protected $stockAddressEntityManager;

    public function __construct(StockAddressEntityManagerInterface $stockAddressEntityManager)
    {
        $this->stockAddressEntityManager = $stockAddressEntityManager;
    }

    public function createStockAddressForStock(StockTransfer $stockTransfer): StockResponseTransfer
    {
        if (!$stockTransfer->getAddress()) {
            return (new StockResponseTransfer())
                ->setStock($stockTransfer)
                ->setIsSuccessful(true);
        }

        return $this->getTransactionHandler()->handleTransaction(function () use ($stockTransfer) {
            return $this->executeCreateStockAddressForStockTransaction($stockTransfer);
        });
    }

    protected function executeCreateStockAddressForStockTransaction(StockTransfer $stockTransfer): StockResponseTransfer
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
