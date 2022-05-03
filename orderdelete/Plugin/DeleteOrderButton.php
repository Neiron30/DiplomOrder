<?php

namespace Eug\orderdelete\Plugin;

use Magento\Sales\Block\Adminhtml\Order\View as OrderView;
use Eug\orderdelete\Helper\Data as DataHelper;

class DeleteOrderButton
{
    public function __construct(
        DataHelper $dataHelper
    ) {
            $this->helper = $dataHelper;
    }

    public function beforeSetLayout(
        OrderView $subject
    ) {
        $enableOrderDelete = $this->helper->getConfig('orderdelete/general/enable');
        if ($enableOrderDelete == 1) {
            $message = "Are you sure you want to delete this order?";

            $subject->addButton(
                'order_delete_button',
                [
                'label' => __('Delete'),
                'class' => __('delete'),
                'id' => 'order-view-delete-button',
                'onclick' => 'confirmSetLocation(\'' . $message . '\',
                \'' . $subject->getUrl('orderdelete/order/delete') . '\')'
                ]
            );
        }
    }
}
