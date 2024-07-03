<?php

namespace MD\DeleteOrders\Model\Order;

use Exception;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Api\CreditmemoRepositoryInterface as CreditmemoRepository;
use Magento\Sales\Api\Data\CreditmemoInterface;
use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\ShipmentInterface;
use Magento\Sales\Api\InvoiceRepositoryInterface as InvoiceRepository;
use Magento\Sales\Api\OrderRepositoryInterface as OrderRepository;
use Magento\Sales\Api\ShipmentRepositoryInterface as ShipmentRepository;
use Magento\Sales\Model\Order;
use Psr\Log\LoggerInterface;

class DeleteOrder
{
    /**
     * @var SearchCriteriaBuilder
     */
    protected SearchCriteriaBuilder $searchCriteriaBuilder;

    /**
     * @var OrderRepository
     */
    protected OrderRepository $orderRepository;

    /**
     * @var CreditmemoRepository
     */
    protected CreditmemoRepository $creditMemoRepository;

    /**
     * @var InvoiceRepository
     */
    protected InvoiceRepository $invoiceRepository;

    /**
     * @var ShipmentRepository
     */
    protected ShipmentRepository $shipmentRepository;

    /**
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * DeleteOrder Constructor.
     *
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param OrderRepository $orderRepository
     * @param CreditmemoRepository $creditMemoRepository
     * @param InvoiceRepository $invoiceRepository
     * @param ShipmentRepository $shipmentRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        OrderRepository       $orderRepository,
        CreditmemoRepository  $creditMemoRepository,
        InvoiceRepository     $invoiceRepository,
        ShipmentRepository    $shipmentRepository,
        LoggerInterface       $logger
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->orderRepository = $orderRepository;
        $this->creditMemoRepository = $creditMemoRepository;
        $this->invoiceRepository = $invoiceRepository;
        $this->shipmentRepository = $shipmentRepository;
        $this->logger = $logger;
    }

    /**
     * Delete all Order Related Information Data (C-Memo,Shipment,Invoice).
     *
     * @param OrderInterface|Order $order
     * @return bool
     */
    public function deleteOrder($order): bool
    {
        $orderId = $order->getId();
        if ($order->hasInvoices()) {
            $invoices = $this->getInvoiceDataByOrderId($orderId);
            foreach ($invoices as $invoice) {
                $this->invoiceRepository->delete($invoice);
            }
        }
        if ($order->hasCreditmemos()) {
            $creditMemos = $this->getCreditMemoDataByOrderId($orderId);
            foreach ($creditMemos as $creditMemo) {
                $this->creditMemoRepository->delete($creditMemo);
            }
        }
        if ($order->hasShipments()) {
            $shipments = $this->getShipmentDataByOrderId($orderId);
            foreach ($shipments as $shipment) {
                $this->shipmentRepository->delete($shipment);
            }
        }

        return $this->orderRepository->delete($order);
    }

    /**
     * Retrieve Shipment by Order id
     *
     * @param int $orderId
     * @return ShipmentInterface[]|null
     */
    public function getShipmentDataByOrderId(int $orderId): ?array
    {
        $searchCriteria = $this->searchCriteriaBuilder->addFilter('order_id', $orderId)->create();
        try {
            $shipments = $this->shipmentRepository->getList($searchCriteria);
            $shipmentRecords = $shipments->getItems();
        } catch (Exception $exception) {
            $this->logger->critical($exception->getMessage());
            $shipmentRecords = null;
        }

        return $shipmentRecords;
    }

    /**
     * Retrieve Invoice by Order id
     *
     * @param int $orderId
     * @return InvoiceInterface[]|null
     */
    public function getInvoiceDataByOrderId(int $orderId): ?array
    {
        $searchCriteria = $this->searchCriteriaBuilder->addFilter('order_id', $orderId)->create();
        try {
            $invoices = $this->invoiceRepository->getList($searchCriteria);
            $invoiceItems = $invoices->getItems();
        } catch (Exception $exception) {
            $this->logger->critical($exception->getMessage());
            $invoiceItems = null;
        }

        return $invoiceItems;
    }

    /**
     * Retrieve Creditmemo by Order id
     *
     * @param int $orderId
     * @return CreditmemoInterface[]|null
     */
    public function getCreditMemoDataByOrderId(int $orderId): ?array
    {
        $searchCriteria = $this->searchCriteriaBuilder->addFilter('order_id', $orderId)->create();
        try {
            $creditMemos = $this->creditMemoRepository->getList($searchCriteria);
            $creditMemoItems = $creditMemos->getItems();
        } catch (Exception $exception) {
            $this->logger->critical($exception->getMessage());
            $creditMemoItems = null;
        }

        return $creditMemoItems;
    }
}
