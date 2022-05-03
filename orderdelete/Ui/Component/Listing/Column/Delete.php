<?php



namespace Eug\orderdelete\Ui\Component\Listing\Column;

use Magento\Ui\Component\Listing\Columns\Column;
use Eug\orderdelete\Helper\Data as DataHelper;
use Magento\Framework\DataObject;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;

class Delete extends Column
{
    public function __construct(
        ContextInterface $context,
        DataHelper $dataHelper,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        $enableOrderDelete = $dataHelper->getConfig('orderdelete/general/enable');
        if ($enableOrderDelete != 1) {
            $data = [];
        }
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }
}
