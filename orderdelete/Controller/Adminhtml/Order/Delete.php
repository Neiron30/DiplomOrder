<?php



namespace Eug\orderdelete\Controller\Adminhtml\Order;

use Magento\Backend\App\Action\Context;
use Eug\orderdelete\Helper\Data as DataHelper;

class Delete extends \Magento\Backend\App\Action
{
    private $order;

    public function __construct(
        Context $context,
        \Magento\Sales\Model\Order $order,
        DataHelper $dataHelper
    ) {
            parent::__construct($context);
            $this->order = $order;
            $this->helper = $dataHelper;
    }

    /**
     * Delete action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */

    public function execute()
    {
        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('order_id');
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            try {
                // init model and delete
                $order = $this->order->load($id);
                $order->delete();
                $this->helper->deleteRecord($order->getId());
                $this->helper->deleteReservationInventory($order->getIncrementId());
                // display success message
                $this->messageManager->addSuccess(__('Order has been deleted.'));
                return $resultRedirect->setPath('sales/order/index');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addError($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('sales/order/view', ['id' => $id]);
            }
        }
        // display error message
        $this->messageManager->addError(__('We can\'t find an item to delete.'));
        // go to grid
        return $resultRedirect->setPath('sales/order/index');
    }
}
