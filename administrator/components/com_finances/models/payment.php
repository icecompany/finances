<?php
defined('_JEXEC') or die;
use Joomla\CMS\MVC\Model\AdminModel;

class FinancesModelPayment extends AdminModel {

    public function getItem($pk = null)
    {
        $item = parent::getItem($pk);
        if ($item->id === null) {
            $item->scoreID = JFactory::getApplication()->getUserState($this->option.'.payment.scoreID');
            $item->dat = JDate::getInstance()->toSql();
        }
        $score = $this->getScore($item->scoreID);
        $contract = $this->getContract($score->contractID);
        if ($item->id === null) {
            $item->title = JText::sprintf('COM_FINANCES_TITLE_PAYMENT_ADD', $score->number, $contract->company, $contract->project);
            $item->amount = $score->debt;
        }
        else {
            $item->title = JText::sprintf('COM_FINANCES_TITLE_PAYMENT_EDIT', $item->number, $score->number, $contract->company, $contract->project);
        }
        return $item;
    }

    public function save($data)
    {
        return parent::save($data);
    }

    public function getTable($name = 'Payments', $prefix = 'TableFinances', $options = array())
    {
        return JTable::getInstance($name, $prefix, $options);
    }

    public function getForm($data = array(), $loadData = true)
    {
        $form = $this->loadForm(
            $this->option.'.payment', 'payment', array('control' => 'jform', 'load_data' => $loadData)
        );
        if (empty($form))
        {
            return false;
        }

        return $form;
    }

    protected function loadFormData()
    {
        $data = JFactory::getApplication()->getUserState($this->option.'.edit.payment.data', array());
        if (empty($data))
        {
            $data = $this->getItem();
        }

        return $data;
    }

    protected function prepareTable($table)
    {
        $all = get_class_vars($table);
        unset($all['_errors']);
        $nulls = ['order_name', 'payerID']; //Поля, которые NULL
        foreach ($all as $field => $v) {
            if (empty($field)) continue;
            if (in_array($field, $nulls)) {
                if (!strlen($table->$field)) {
                    $table->$field = NULL;
                    continue;
                }
            }
            if (!empty($field)) $table->$field = trim($table->$field);
        }
        $table->dat = JDate::getInstance($table->dat)->toSql();

        parent::prepareTable($table);
    }

    private function getScore(int $scoreID)
    {
        $table = parent::getTable('Scores', 'TableFinances');
        $table->load($scoreID);
        return $table;
    }

    private function getContract(int $contractID)
    {
        JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . "/components/com_contracts/models");
        JTable::addIncludePath(JPATH_ADMINISTRATOR . "/components/com_contracts/tables");
        $model = JModelLegacy::getInstance('Contract', 'ContractsModel');
        return $model->getItem($contractID);
    }

    protected function canEditState($record)
    {
        $user = JFactory::getUser();

        if (!empty($record->id))
        {
            return $user->authorise('core.edit.state', $this->option . '.payment.' . (int) $record->id);
        }
        else
        {
            return parent::canEditState($record);
        }
    }

    public function getScript()
    {
        return 'administrator/components/' . $this->option . '/models/forms/payment.js';
    }
}