<?php



namespace Eug\orderdelete\Helper;

use Magento\Sales\Model\ResourceModel\OrderFactory;
use Magento\InventoryReservationCli\Model\ResourceModel\GetReservationsListFactory;
use Magento\Framework\App\ResourceConnection;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    private $orderResourceFactory;

    private $connection;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        OrderFactory $orderResourceFactory,
        GetReservationsListFactory $collection,
        ResourceConnection $connection
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->orderResourceFactory = $orderResourceFactory;
        $this->collection = $collection;
        $this->connection = $connection;
    }

    public function getConfig($config_path)
    {
        return $this->scopeConfig->getValue(
            $config_path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function deleteRecord($orderId)
    {
        /** @var \Magento\Sales\Model\ResourceModel\Order $resource */
        $resource = $this->orderResourceFactory->create();
        $connection = $resource->getConnection();

        /** delete invoice grid record via resource model*/
        $connection->delete(
            $resource->getTable('sales_invoice_grid'),
            $connection->quoteInto('order_id = ?', $orderId)
        );

        /** delete shipment grid record via resource model*/
        $connection->delete(
            $resource->getTable('sales_shipment_grid'),
            $connection->quoteInto('order_id = ?', $orderId)
        );

        /** delete creditmemo grid record via resource model*/
        $connection->delete(
            $resource->getTable('sales_creditmemo_grid'),
            $connection->quoteInto('order_id = ?', $orderId)
        );
    }

    public function deleteReservationInventory($incrementId)
    {
        /** @var \Magento\InventoryReservationCli\Model\ResourceModel\GetReservationsListFactory $collection */
        $collection = $this->collection->create()->execute();
        $adapter = $this->connection->getConnection();
        $table = $this->connection->getTableName('inventory_reservation');
        foreach ($collection as $value) {
            $metaData = json_decode($value['metadata'], 1);
            if ((int)$metaData['object_increment_id'] == (int)$incrementId) {
                $adapter->delete($table, $adapter->quoteInto('metadata LIKE ?', '%'.$incrementId.'%'));
            }
        }
    }
}
