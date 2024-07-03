<?php

namespace MD\DeleteOrders\Controller\Adminhtml\Order;

use Exception;
use MD\DeleteOrders\Model\Order\DeleteOrder;
use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultInterface;
use Magento\Sales\Api\OrderRepositoryInterface as OrderRepository;

/**
 * class Order for delete order on sales order view page
 */
class Order extends Action
{
    /**
     * Authorization level of a basic admin session
     */
    public const ADMIN_RESOURCE = 'Md_DeleteOrders::md_delete_orders';

    /**
     * @var OrderRepository
     */
    protected OrderRepository $orderRepository;

    /**
     * @var DeleteOrder
     */
    protected DeleteOrder $deleteOrder;

    /**
     * Order constructor.
     *
     * @param Action\Context $context
     * @param OrderRepository $orderRepository
     * @param DeleteOrder $deleteOrder
     */
    public function __construct(
        Action\Context  $context,
        OrderRepository $orderRepository,
        DeleteOrder     $deleteOrder
    ) {
        $this->deleteOrder = $deleteOrder;
        $this->orderRepository = $orderRepository;
        parent::__construct($context);
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        $order = $this->orderRepository->get($orderId);
        $incrementId = $order->getIncrementId();
        try {
            $this->deleteOrder->deleteOrder($order);
            $this->messageManager->addSuccessMessage(__('Successfully deleted order #%1.', $incrementId));
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage(__('Error delete order #%1.', $incrementId));
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('sales/order/');

        return $resultRedirect;
    }
}
