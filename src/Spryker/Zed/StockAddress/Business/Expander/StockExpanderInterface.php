<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\StockAddress\Business\Expander;

use Generated\Shared\Transfer\StockTransfer;

interface StockExpanderInterface
{
    public function expandStockTransfer(StockTransfer $stockTransfer): StockTransfer;
}
