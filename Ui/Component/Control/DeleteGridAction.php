<?php

namespace MD\DeleteOrders\Ui\Component\Control;

use Magento\Ui\Component\Control\Action;

/**
 * class DeleteGridAction To check url action Delete Order, Invoice, Shipment And Credit memo
 */
class DeleteGridAction extends Action
{
    /**
     * @inheritdoc
     */
    public function prepare(): void
    {
        $config = $this->getConfiguration();
        $context = $this->getContext();
        $config['url'] = $context->getUrl(
            $config['deleteAction'],
            ['order_id' => $context->getRequestParam('order_id')]
        );
        $this->setData('config', $config);
        parent::prepare();
    }
}
