<?php

namespace wobef\classes\providers\order;

use wobef\classes\providers\column\OrderColumnProvider;
use wobef\classes\repositories\Column;
use wobef\classes\repositories\Order;

class OrderProvider
{
    private static $instance = null;
    private $order_repository;

    public static function get_instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct()
    {
        $this->order_repository = new Order();
    }

    public function get_items($items, $children, $columns)
    {
        return $this->items($items, $children, $columns);
    }

    private function items($items, $children, $columns)
    {
        if (!empty($items)) {
            $column_provider = OrderColumnProvider::get_instance();
            $show_id_column = Column::SHOW_ID_COLUMN;
            $orders = $this->order_repository->get_wc_orders($items);
            if (!empty($orders)) {
                foreach ($orders as $item) {
                    include WOBEF_VIEWS_DIR . "data_table/row.php";
                }
            }
        }
    }
}
