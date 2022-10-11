<?php
/**
 * This file is part of the ExampleHyvaTheme package.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade ExampleHyvaTheme
 * to newer versions in the future.
 *
 * @copyright Copyright (c) 2022 Magebit, Ltd. (https://magebit.com/)
 * @author    Magebit <info@magebit.com>
 * @license   MIT
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Magebit\Example\Block\Order;

use Magento\Sales\Block\Order\Recent;
use Magento\Framework\View\Element\Template\Context;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactoryInterface;
use Magento\Customer\Model\Session;
use Magento\Sales\Model\Order\Config;
use Magento\Sales\Helper\Reorder;
use Magento\Framework\Data\Helper\PostHelper;

class ExtendedRecent extends Recent
{
    /**
     * @var CollectionFactoryInterface
     */
    protected $_orderCollectionFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Sales\Model\Order\Config
     */
    protected $_orderConfig;

    /**
     * @var PostHelper
     */
    private PostHelper $helperPost;

    /**
     * @var Reorder
     */
    private Reorder $helperReorder;

    public function __construct(
        Context $context,
        Session $customerSession,
        Config $orderConfig,
        PostHelper $helperPost,
        Reorder $helperReorder,
        CollectionFactoryInterface $orderCollectionFactory,
        array $data = []
        )
    {
        parent::__construct($context, $orderCollectionFactory, $customerSession, $orderConfig, $data);

        $this->_orderCollectionFactory = $orderCollectionFactory;
        $this->_customerSession = $customerSession;
        $this->helperReorder = $helperReorder;
        $this->helperPost = $helperPost;
        $this->_orderConfig = $orderConfig;
    }
    /**
     * Get recently placed orders formatted for Alpine
     */
    public function customDataOrders()
    {
        $orders = parent::getOrders();
        $adjustedOrderData = [];

        foreach ($orders as $order) {
            $formData = json_decode($this->helperPost->getPostData($this->getReorderUrl($order)), true);

            $adjustedOrderData[] = [
                'entity_id' => $order->getEntityId(),
                'increment_id' => $order->getIncrementId(),
                'created_at' => $this->formatDate($order->getCreatedAt()),
                'customer_name' => $order->getCuStomerFirstName() . " " . $order->getCustomerLastName(),
                'grand_total' => $order->getGrandTotal(),
                'status' => $order->getStatus(),
                'can_reorder' => $this->helperReorder->canReorder($order->getEntityId()),
                'reorder_action' => $formData['action'],
                'reorder_data' => json_encode($formData['data']),
                'po_number' => $order->getPayment()->getPoNumber()
            ];
        }

        return $adjustedOrderData;
    }
}