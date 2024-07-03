<?php

namespace MD\DeleteOrders\Controller\Adminhtml\Creditmemo;

use Exception;
use Md\DeleteOrders\Helper\Data as DataHelper;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Sales\Api\CreditmemoRepositoryInterface as CreditmemoRepository;
use Magento\Sales\Api\OrderManagementInterface as OrderManagement;
use Magento\Sales\Controller\Adminhtml\Order\AbstractMassAction;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Sales\Model\ResourceModel\Order\Creditmemo\CollectionFactory as MemoCollectionFactory;
use Magento\Ui\Component\MassAction\Filter;

/**
 * Class MassDelete for delete credit memo
 */
class MassDelete extends AbstractMassAction implements HttpPostActionInterface
{
    /**
     * Authorization level of a basic admin session
     */
    public const ADMIN_RESOURCE = 'MD_DeleteOrders::creditmemo';

    /**
     * @var OrderManagement
     */
    protected OrderManagement $orderManagement;

    /**
     * @var MemoCollectionFactory
     */
    protected MemoCollectionFactory $memoCollectionFactory;

    /**
     * @var CreditmemoRepository
     */
    protected CreditmemoRepository $creditMemoRepository;

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
     * @param OrderManagement $orderManagement
     * @param MemoCollectionFactory $memoCollectionFactory
     * @param CreditmemoRepository $creditMemoRepository
     * @param DataHelper $dataHelper
     */
    public function __construct(
        Context               $context,
        Filter                $filter,
        CollectionFactory     $collectionFactory,
        OrderManagement       $orderManagement,
        MemoCollectionFactory $memoCollectionFactory,
        CreditmemoRepository  $creditMemoRepository,
        DataHelper            $dataHelper
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->orderManagement = $orderManagement;
        $this->memoCollectionFactory = $memoCollectionFactory;
        $this->creditMemoRepository = $creditMemoRepository;
        $this->dataHelper = $dataHelper;
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
            if ($this->dataHelper->isDeleteOrderCreditmemo()) {
                $selected = [];
                $collectionMemo = $this->filter->getCollection($this->memoCollectionFactory->create());
                foreach ($collectionMemo as $memo) {
                    $selected[] = $memo->getId();
                }

                if ($selected) {
                    foreach ($selected as $creditMemoId) {
                        $creditMemo = $this->creditMemoRepository->get($creditMemoId);
                        try {
                            $this->creditMemoRepository->delete($creditMemo);
                            $this->messageManager->addSuccessMessage(
                                __('Successfully deleted credit memo #%1.', $creditMemo->getIncrementId())
                            );
                        } catch (Exception $e) {
                            $this->messageManager->addErrorMessage(
                                __('Error delete credit memo #%1.', $creditMemo->getIncrementId())
                            );
                        }
                    }
                }
            } else {
                $this->messageManager->addErrorMessage(__('Delete Order Credit Memo is Disabled'));
                return $resultRedirect->setPath('sales/creditmemo/index');
            }
        } else {
            $this->messageManager->addErrorMessage(__('Delete Order Module is Disabled'));
        }

        if ($params['namespace'] == 'sales_order_view_creditmemo_grid') {
            $resultRedirect->setPath('sales/order/view', ['order_id' => $orderId]);
        } else {
            $resultRedirect->setPath('sales/creditmemo/');
        }

        return $resultRedirect;
    }
}
