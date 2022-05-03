<?php


namespace Eug\orderdelete\Controller\Adminhtml\Order;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Eug\orderdelete\Helper\Data as DataHelper;

class MassDelete extends \Magento\Sales\Controller\Adminhtml\Order\AbstractMassAction
{
    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     */

    private $helper;

    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        DataHelper $dataHelper
    ) {
        parent::__construct($context, $filter);
        $this->collectionFactory = $collectionFactory;
        $this->helper = $dataHelper;
    }

    /**
     * Delete selected orders
     *
     * @param AbstractCollection $collection
     * @return \Magento\Backend\Model\View\Result\Redirect
     */

    protected function massAction(AbstractCollection $collection)
    {
        $countCancelOrder = 0;
        foreach ($collection->getItems() as $order) {
            $this->deleteItems($order);
            $this->helper->deleteRecord($order->getId());
            $this->helper->deleteReservationInventory($order->getIncrementId());
            $countCancelOrder++;
        }

        if ($countCancelOrder) {
            $this->messageManager->addSuccess(__('We deleted %1 order(s).', $countCancelOrder));
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath($this->getComponentRefererUrl());
        return $resultRedirect;
    }

    private function deleteItems($item)
    {
         $item->delete();
    }
}
