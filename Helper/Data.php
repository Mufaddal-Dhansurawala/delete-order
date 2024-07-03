<?php

namespace MD\DeleteOrders\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Helper Data to get system configuration values
 */
class Data extends AbstractHelper
{
    /**#@+
     * Constants defined to get the value of store Config.
     */
    public const DELETE_ORDERS_ENABLE = 'mi_delete_orders/general/enable_disable';
    public const DELETE_ORDERS_CREDIT_MEMO = 'mi_delete_orders/general/creditmemo';
    public const DELETE_ORDERS_INVOICE = 'mi_delete_orders/general/invoice';
    public const DELETE_ORDERS_SHIPMENT = 'mi_delete_orders/general/shipment';
    /**#@-*/

    /**
     * Module Enabled or Disabled
     *
     * @return string
     */
    public function isEnable(): string
    {
        return $this->scopeConfig->getValue(
            self::DELETE_ORDERS_ENABLE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Enable Delete Order Credit memo
     *
     * @return string
     */
    public function isDeleteOrderCreditmemo(): string
    {
        return $this->scopeConfig->getValue(
            self::DELETE_ORDERS_CREDIT_MEMO,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Enable Delete Order Invoice
     *
     * @return string
     */
    public function isDeleteOrderInvoice(): string
    {
        return $this->scopeConfig->getValue(
            self::DELETE_ORDERS_INVOICE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Enable Delete Order Shipment
     *
     * @return string
     */
    public function isDeleteOrderShipment(): string
    {
        return $this->scopeConfig->getValue(
            self::DELETE_ORDERS_SHIPMENT,
            ScopeInterface::SCOPE_STORE
        );
    }
}
