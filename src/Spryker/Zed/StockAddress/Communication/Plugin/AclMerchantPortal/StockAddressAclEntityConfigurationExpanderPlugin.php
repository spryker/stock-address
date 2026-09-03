<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Zed\StockAddress\Communication\Plugin\AclMerchantPortal;

use Generated\Shared\Transfer\AclEntityMetadataConfigTransfer;
use Generated\Shared\Transfer\AclEntityMetadataTransfer;
use Generated\Shared\Transfer\AclEntityParentMetadataTransfer;
use Spryker\Zed\AclMerchantPortalExtension\Dependency\Plugin\AclEntityConfigurationExpanderPluginInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * Without this plugin `SpyStockAddress` is absent from the composed `AclEntityMetadataConfig`, so an
 * ACL-scoped read of it in a Merchant Portal request yields nothing. `StockExpander::expandStockTransfer()`
 * then returns the stock with a null `address`, and that null travels silently through
 * `MerchantStockAddressTransfer.stockAddress` and `VertexShippingWarehouseTransfer.warehouseAddress`
 * until `VertexSuppliesLineItemsBuilder` demands it - a Merchant Portal refund, for example.
 *
 * @method \Spryker\Zed\StockAddress\Business\StockAddressFacadeInterface getFacade()
 * @method \Spryker\Zed\StockAddress\StockAddressConfig getConfig()
 */
class StockAddressAclEntityConfigurationExpanderPlugin extends AbstractPlugin implements AclEntityConfigurationExpanderPluginInterface
{
    protected const string ENTITY_STOCK = 'Orm\Zed\Stock\Persistence\SpyStock';

    protected const string ENTITY_STOCK_ADDRESS = 'Orm\Zed\StockAddress\Persistence\SpyStockAddress';

    /**
     * {@inheritDoc}
     * - Expands provided `AclEntityMetadataConfig` transfer object with stock address composite data.
     * - Registers `SpyStockAddress` as a sub-entity of `SpyStock`, so it inherits the stock's ACL scope.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\AclEntityMetadataConfigTransfer $aclEntityMetadataConfigTransfer
     *
     * @return \Generated\Shared\Transfer\AclEntityMetadataConfigTransfer
     */
    public function expand(AclEntityMetadataConfigTransfer $aclEntityMetadataConfigTransfer): AclEntityMetadataConfigTransfer
    {
        $aclEntityMetadataConfigTransfer
            ->getAclEntityMetadataCollectionOrFail()
            ->addAclEntityMetadata(
                static::ENTITY_STOCK_ADDRESS,
                (new AclEntityMetadataTransfer())
                    ->setEntityName(static::ENTITY_STOCK_ADDRESS)
                    ->setIsSubEntity(true)
                    ->setParent((new AclEntityParentMetadataTransfer())->setEntityName(static::ENTITY_STOCK)),
            );

        return $aclEntityMetadataConfigTransfer;
    }
}
