<?php
use Joomla\CMS\Table\Table;
defined('_JEXEC') or die;

class TableFinancesPayments extends Table
{
    var $id = null;
    var $dat = null;
    var $scoreID = null;
    var $order_name = null;
    var $amount = null;
    var $payerID = null;

    public function __construct(JDatabaseDriver $db)
    {
        parent::__construct('#__mkv_payments', 'id', $db);
    }

    public function store($updateNulls = true)
    {
        return parent::store(true);
    }
}