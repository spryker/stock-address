<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\StockAddress\Persistence;

use Generated\Shared\Transfer\StockAddressTransfer;

interface StockAddressEntityManagerInterface
{
    public function saveStockAddress(StockAddressTransfer $stockAddressTransfer): StockAddressTransfer;

    public function deleteStockAddressForStock(int $idStock): void;
}
