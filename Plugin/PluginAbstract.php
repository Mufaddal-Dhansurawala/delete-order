<?php

namespace MD\DeleteOrders\Plugin;

use Magento\Authorization\Model\Acl\AclRetriever;
use Magento\Backend\Model\Auth\Session;

/**
 * class PluginAbstract To check ACL
 */
class PluginAbstract
{
    /**
     * @var AclRetriever
     */
    protected AclRetriever $aclRetriever;

    /**
     * @var Session
     */
    protected Session $authSession;

    /**
     * PluginAbstract constructor.
     *
     * @param AclRetriever $aclRetriever
     * @param Session $authSession
     */
    public function __construct(
        AclRetriever $aclRetriever,
        Session      $authSession
    ) {
        $this->aclRetriever = $aclRetriever;
        $this->authSession = $authSession;
    }

    /**
     * Allowed Resources
     *
     * @return bool
     */
    public function isAllowedResources(): bool
    {
        $user = $this->authSession->getUser();
        $role = $user->getRole();
        $resources = $this->aclRetriever->getAllowedResourcesByRole($role->getId());
        if (in_array("Magento_Backend::all", $resources)
            || in_array("MD_DeleteOrders::md_delete_orders", $resources)) {
            return true;
        }

        return false;
    }
}
