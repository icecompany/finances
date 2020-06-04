<?php
use Joomla\CMS\Table\Table;
defined('_JEXEC') or die;

class TableFinancesScores extends Table
{
    var $id = null;
    var $dat = null;
    var $contractID = null;
    var $number = null;
    var $amount = null;
    var $payments = null;
    var $debt = null;
    var $status = null;

    public function __construct(JDatabaseDriver $db)
    {
        parent::__construct('#__mkv_scores', 'id', $db);
    }

    public function store($updateNulls = true)
    {
        return parent::store(true);
    }
}