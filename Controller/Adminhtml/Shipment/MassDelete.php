<?php

namespace MD\DeleteOrders\Controller\Adminhtml\Shipment;

use Exception;
use MD\DeleteOrders\Helper\Data as DataHelper;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Sales\Api\OrderManagementInterface;
use Magento\Sales\Api\ShipmentRepositoryInterface as ShipmentRepository;
use Magento\Sales\Controller\Adminhtml\Order\AbstractMassAction;
use Magento\Sales\Model\Order\Shipment;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory as ShipmentCollectionFactory;
use Magento\Ui\Component\MassAction\Filter;

/**
 * class MassDelete for delete order shipment
 */
class MassDelete extends AbstractMassAction implements HttpPostActionInterface
{
    /**
     * Authorization level of a basic admin session
     */
    public const ADMIN_RESOURCE = 'MD_DeleteOrders::shipment';

    /**
     * @var OrderManagementInterface
     */
    protected OrderManagementInterface $orderManagement;

    /**
     * @var ShipmentCollectionFactory
     */
    protected ShipmentCollectionFactory $shipmentCollectionFactory;

    /**
     * @var Shipment
     */
    protected Shipment $shipment;

    /**
     * @var ShipmentRepository
     */
    protected ShipmentRepository $shipmentRepository;

    /**
     * @var DataHelper
     */
    protected DataHelper $dataHelper;

    /**
     * MassDelete constructor.
     *
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param OrderManagementInterface $orderManagement
     * @param ShipmentCollectionFactory $shipmentCollectionFactory
     * @param Shipment $shipment
     * @param ShipmentRepository $shipmentRepository
     * @param DataHelper $dataHelper
     */
    public function __construct(
        Context                   $context,
        Filter                    $filter,
        CollectionFactory         $collectionFactory,
        OrderManagementInterface  $orderManagement,
        ShipmentCollectionFactory $shipmentCollectionFactory,
        Shipment                  $shipment,
        ShipmentRepository        $shipmentRepository,
        DataHelper                $dataHelper
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->orderManagement = $orderManagement;
        $this->shipmentCollectionFactory = $shipmentCollectionFactory;
        $this->shipment = $shipment;
        $this->dataHelper = $dataHelper;
        $this->shipmentRepository = $shipmentRepository;
        parent::__construct($context, $filter);
    }

    /**
     * @inheritdoc
     */
    protected function massAction(AbstractCollection $collection)
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $params = $this->getRequest()->getParams();
        $orderId = $this->getRequest()->getParam('order_id');

        if ($this->dataHelper->isEnable()) {
            if ($this->dataHelper->isDeleteOrderShipment()) {
                $params = $this->getRequest()->getParams();
                $selected = [];
                $collectionShipment = $this->filter->getCollection($this->shipmentCollectionFactory->create());
                foreach ($collectionShipment as $shipment) {
                    $selected[] = $shipment->getId();
                }
                if ($selected) {
                    foreach ($selected as $shipmentId) {
                        $shipment = $this->shipmentRepository->get($shipmentId);
                        try {
                            $this->shipmentRepository->delete($shipment);
                            $this->messageManager->addSuccessMessage(
                                __('Successfully deleted shipment #%1.', $shipment->getIncrementId())
                            );
                        } catch (Exception $e) {
                            $this->messageManager->addErrorMessage(
                                __('Error delete shipment #%1.', $shipment->getIncrementId())
                            );
                        }
                    }
                }
            } else {
                $this->messageManager->addErrorMessage(__('Delete Order Shipment is Disabled'));
                return $resultRedirect->setPath('sales/shipment/index');
            }
        } else {
            $this->messageManager->addErrorMessage(__('Delete Order module is Disabled'));
        }

        $resultRedirect->setPath('sales/shipment/');
        if ($params['namespace'] == 'sales_order_view_shipment_grid') {
            $resultRedirect->setPath('sales/order/view', ['order_id' => $orderId]);
        } else {
            $resultRedirect->setPath('sales/shipment/');
        }

        return $resultRedirect;
    }
}
