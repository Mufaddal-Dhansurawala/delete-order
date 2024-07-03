<?php

namespace MD\DeleteOrders\Plugin;

use MD\DeleteOrders\Helper\Data as DataHelper;
use Magento\Framework\App\Config\ScopeConfigInterface as ScopeConfig;
use Magento\Framework\App\Request\Http;
use Magento\Framework\View\Element\UiComponentInterface;
use Magento\Ui\Component\MassAction;

/**
 * class PluginMassAction To check action
 * and set enable disable delete button functionality on order view,invoice, shipment, credit memo page
 */
class PluginMassAction
{
    /**
     * @var ScopeConfig
     */
    protected ScopeConfig $scopeConfig;

    /**
     * @var Http
     */
    protected Http $request;

    /**
     * @var UiComponentInterface[]
     */
    protected array $components;

    /**
     * @var DataHelper
     */
    protected DataHelper $dataHelper;

    /**
     * PluginMassAction constructor.
     *
     * @param ScopeConfig $scopeConfig
     * @param Http $request
     * @param DataHelper $dataHelper
     */
    public function __construct(
        ScopeConfig $scopeConfig,
        Http        $request,
        DataHelper  $dataHelper
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->request = $request;
        $this->dataHelper = $dataHelper;
    }

    /**
     * Around Plugin on prepare Function
     *
     * @param MassAction $subject
     * @param callable $proceed
     * @return void
     */
    public function aroundPrepare(MassAction $subject, callable $proceed): void
    {
        $config = $subject->getConfiguration();
        foreach ($subject->getChildComponents() as $actionComponent) {
            $componentConfig = $actionComponent->getConfiguration();
            $disabledAction = $componentConfig['actionDisable'] ?? false;
            if ($disabledAction) {
                continue;
            }

            $isEnable = $this->dataHelper->isEnable();
            $isEnableShipment = $this->dataHelper->isDeleteOrderShipment();
            $isEnableInvoice = $this->dataHelper->isDeleteOrderInvoice();
            $isEnableCreditMemo = $this->dataHelper->isDeleteOrderCreditmemo();

            $actionType = $componentConfig['type'];
            $defaultActions = in_array(
                $actionType,
                ['deleteShipment', 'deleteInvoice', 'deleteCreditmemo']
            );
            if ($defaultActions) {
                if (($isEnable && $isEnableShipment && $actionType == "deleteShipment") ||
                    ($isEnable && $isEnableInvoice && $actionType == "deleteInvoice") ||
                    ($isEnable && $isEnableCreditMemo && $actionType == "deleteCreditmemo")
                ) {
                    $config['actions'][] = $componentConfig;
                }
            } else {
                $config['actions'][] = $componentConfig;
            }
        }

        $origConfig = $subject->getConfiguration();
        if ($origConfig !== $config) {
            $config = array_replace_recursive($config, $origConfig);
        }

        $subject->setData('config', $config);
        $this->components = [];
    }
}
