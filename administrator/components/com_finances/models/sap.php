<?php
defined('_JEXEC') or die;
use Joomla\CMS\MVC\Model\ListModel;

/**
 * @package Модель лдя получения счетов и платежей из сделки (по конкретной сделке)
 *
 * @since version 2.0.6
 */

class FinancesModelSap extends ListModel
{
    public function __construct(array $config)
    {
        if (empty($config['filter_fields']))
        {
            $config['filter_fields'] = array(
                's.id',
            );
        }
        parent::__construct($config);
        $this->contractID = $config['contractID'];
    }

    protected function _getListQuery()
    {
        $query = $this->_db->getQuery(true);
        $query
            ->select("s.*")
            ->select("p.id as paymentID, p.dat as payment_date, p.order_name, p.amount as payment_amount")
            ->select("c.currency")
            ->from("#__mkv_scores s")
            ->leftJoin("#__mkv_payments p on p.scoreID = s.id")
            ->leftJoin("#__mkv_contracts c on c.id = s.contractID");

        if (is_numeric($this->contractID)) {
            $query->where("s.contractID = {$this->_db->q($this->contractID)}");
        }
        $query->order("s.id DESC");
        $this->setState('list.limit', 0);

        return $query;
    }

    public function getItems()
    {
        $items = parent::getItems();
        $result = [];
        foreach ($items as $item)
        {
            $currency = mb_strtoupper($item->currency);
            if (!isset($result[$item->id])) {
                $result[$item->id] = [];
                $result[$item->id]['id'] = $item->id;
                $result[$item->id]['number'] = $item->number;
                $result[$item->id]['dat'] = JDate::getInstance($item->dat)->format("d.m.Y");
                $amount = number_format((float) $item->amount, 2, '.', ' ');
                $result[$item->id]['amount'] = JText::sprintf("COM_MKV_AMOUNT_{$currency}_SHORT", $amount);
                $payment = number_format((float) $item->payments, 2, '.', ' ');
                $result[$item->id]['payment'] = JText::sprintf("COM_MKV_AMOUNT_{$currency}_SHORT", $payment);
                $debt = number_format((float) $item->debt, 2, '.', ' ');
                $result[$item->id]['debt'] = JText::sprintf("COM_MKV_AMOUNT_{$currency}_SHORT", $debt);
                $color = FinancesHelper::getPaymentStatusColor($item->status);
                $status_text = JText::sprintf("COM_MKV_PAYMENT_STATUS_{$item->status}");
                $result[$item->id]['status'] = "<span style='color: {$color}'>{$status_text}</span>";
                $result[$item->id]['payments'] = [];
            }
            if (empty($item->payment_amount)) continue;
            $arr = [];
            $arr['paymentID'] = $item->paymentID;
            $arr['order_name'] = $item->order_name;
            $arr['payment_date'] = JDate::getInstance($item->payment_date)->format("d.m.Y");
            $payment_amount = number_format((float) $item->payment_amount, 2, '.', ' ');
            $arr['payment_amount'] = JText::sprintf("COM_MKV_AMOUNT_{$currency}_SHORT", $payment_amount);
            $result[$item->id]['payments'][] = $arr;
        }
        return $result;
    }

    /* Сортировка по умолчанию */
    protected function populateState($ordering = 's.id', $direction = 'DESC')
    {
        parent::populateState($ordering, $direction);
    }

    private $contractID;
}