<?php
defined('_JEXEC') or die;
use Joomla\CMS\MVC\Model\ListModel;

class FinancesModelScores extends ListModel
{
    public function __construct(array $config)
    {
        if (empty($config['filter_fields']))
        {
            $config['filter_fields'] = array(
                's.dat',
                's.number',
                'contract_number',
                'c.amount',
                's.amount',
                's.status',
                'contract_status',
                'status',
                'manager',
                'search',
                'e.title',
                's.payments',
                's.debt',
                'c.dat',
            );
        }
        parent::__construct($config);
        $this->raw = false;
        $input = JFactory::getApplication()->input;
        if ($input->getInt('contractID', 0) > 0) {
            $this->contractID = $input->getInt('contractID');
        }
        if (isset($config['contractID'])) {
            $this->contractID = $config['contractID'];
            $this->raw = true;
        }
    }

    protected function _getListQuery()
    {
        $query = $this->_db->getQuery(true);
        $query
            ->select("e.title as company")
            ->select("s.*")
            ->select("ifnull(c.number_free, c.number) as contract_number, c.dat as contract_date")
            ->select("c.amount as contract_amount, c.currency, c.companyID, c.id as contractID")
            ->from("#__mkv_scores s")
            ->rightJoin("#__mkv_contracts c on c.id = s.contractID")
            ->leftJoin("#__mkv_companies e on e.id = c.companyID")
            ->where("c.status in (1, 5, 10)")
            ->group("s.number, s.contractID, c.companyID");
        if (is_numeric($this->contractID)) {
            $query->where("s.contractID = {$this->_db->q($this->contractID)}");
        }
        if (!$this->raw) {
            /* Фильтр */
            $search = $this->getState('filter.search');
            if (!empty($search)) {
                if (stripos($search, 'num:') !== false || stripos($search, '#') !== false || stripos($search, '№') !== false) { //Поиск по номеру договора
                    $delimiter = ":";
                    if (stripos($search, 'num:') !== false) $delimiter = ":";
                    if (stripos($search, '#') !== false) $delimiter = "#";
                    if (stripos($search, '№') !== false) $delimiter = "№";
                    $num = explode($delimiter, $search);
                    $num = $num[1];
                    if (is_numeric($num)) {
                        $query->where("c.number = {$this->_db->q($num)}");
                    }
                } else {
                    $text = $this->_db->q("%{$search}%");
                    $query->where("(e.title like {$text} or e.title_full like {$text} or e.title_en like {$text} or s.number like {$text})");
                }
            }
            // Фильтруем по состоянию оплаты.
            $status = $this->getState('filter.status');
            if (is_numeric($status)) {
                if ($status < 0) {
                    $query->where("s.id is null");
                }
                else {
                    $query->where("s.status = {$this->_db->q($status)}");
                }
            }
            // Фильтруем по менеджеру.
            $manager = $this->getState('filter.manager');
            if (is_numeric($manager)) {
                $query->where("c.managerID = {$this->_db->q($manager)}");
            }
            $project = PrjHelper::getActiveProject();
            if (is_numeric($project)) {
                $query->where("c.projectID = {$this->_db->q($project)}");
            }

            $contract_status = $this->getState('filter.contract_status');
            if (is_array($contract_status) && !empty($contract_status)) {
                $contract_status = implode(", ", $contract_status);
                if (in_array(101, $status)) {
                    $query->where("(c.status in ({$contract_status}) or c.status is null)");
                } else {
                    $query->where("c.status in ({$contract_status})");
                }
            }

            /* Сортировка */
            $orderCol  = $this->state->get('list.ordering');
            $orderDirn = $this->state->get('list.direction');
            $limit = $this->getState('list.limit');
            if ($orderCol == 's.number') {
                if ($orderDirn == 'ASC') $orderCol = "LENGTH(s.number) ASC, s.number";
                if ($orderDirn == 'DESC') $orderCol = "LENGTH(s.number) DESC, s.number";
            }
            if ($orderCol == 'contract_number') {
                if ($orderDirn == 'ASC') $orderCol = "LENGTH(contract_number) ASC, contract_number";
                if ($orderDirn == 'DESC') $orderCol = "LENGTH(contract_number) DESC, contract_number";
            }
        }
        else {
            $orderCol  = 's.id';
            $orderDirn = 'desc';
            $limit = 0;
        }
        $query->order($this->_db->escape($orderCol . ' ' . $orderDirn));
        $this->setState('list.limit', $limit);

        return $query;
    }

    public function getItems()
    {
        $items = parent::getItems();
        $result = ['items' => [], 'amount' => [], 'contracts_amount' => []];
        $return = PrjHelper::getReturnUrl();
        foreach ($items as $item)
        {
            $arr = [];
            $arr['id'] = $item->id;
            $arr['number'] = $item->number;
            $arr['company'] = $item->company;
            $arr['contract'] = MkvHelper::getContractSmallTitle($item->contract_number ?? '', '');
            $arr['dat'] = JDate::getInstance($item->dat)->format("d.m.Y");
            $arr['contract_date'] = JDate::getInstance($item->contract_date)->format("d.m.Y");
            $currency = mb_strtoupper($item->currency);
            $arr['amount_clean'] = $item->amount;
            $amount = number_format((float) $item->amount, MKV_FORMAT_DEC_COUNT, MKV_FORMAT_SEPARATOR_FRACTION, MKV_FORMAT_SEPARATOR_DEC);
            $arr['amount'] = JText::sprintf("COM_MKV_AMOUNT_{$currency}_SHORT", $amount);
            $arr['payments_clean'] = $item->payments;
            $payments = number_format((float) $item->payments, MKV_FORMAT_DEC_COUNT, MKV_FORMAT_SEPARATOR_FRACTION, MKV_FORMAT_SEPARATOR_DEC);
            $arr['payments'] = JText::sprintf("COM_MKV_AMOUNT_{$currency}_SHORT", $payments);
            $arr['debt_clean'] = $item->debt;
            $debt = number_format((float) $item->debt, MKV_FORMAT_DEC_COUNT, MKV_FORMAT_SEPARATOR_FRACTION, MKV_FORMAT_SEPARATOR_DEC);
            $arr['debt'] = JText::sprintf("COM_MKV_AMOUNT_{$currency}_SHORT", $debt);
            $arr['status_clean'] = JText::sprintf("COM_MKV_PAYMENT_STATUS_{$item->status}");
            $contract_amount = (!is_null($item->contract_amount)) ? number_format((float) $item->contract_amount, MKV_FORMAT_DEC_COUNT, MKV_FORMAT_SEPARATOR_FRACTION, MKV_FORMAT_SEPARATOR_DEC) : '';
            $arr['contract_amount_clean'] = $contract_amount;
            $arr['contract_amount'] = JText::sprintf("COM_MKV_AMOUNT_{$currency}_SHORT", $contract_amount);
            $arr['color'] = FinancesHelper::getPaymentStatusColor($item->status);
            $arr['status'] = "<span style='color: {$arr['color']}'>{$arr['status_clean']}</span>";
            $url = JRoute::_("index.php?option={$this->option}&amp;task=score.edit&amp;id={$item->id}&amp;return={$return}");
            $arr['edit_link'] = JHtml::link($url, $item->number);
            $url = JRoute::_("index.php?option=com_companies&amp;task=company.edit&amp;id={$item->companyID}&amp;return={$return}");
            $arr['company_link'] = JHtml::link($url, $item->company);
            $url = JRoute::_("index.php?option=com_contracts&amp;task=contract.edit&amp;id={$item->contractID}&amp;return={$return}");
            $arr['contract_link'] = JHtml::link($url, $arr['contract']);
            $url = JRoute::_("index.php?option={$this->option}&amp;task=payment.add&amp;scoreID={$item->id}&amp;return={$return}");
            $arr['payment_link'] = JHtml::link($url, JText::sprintf('COM_FINANCES_LINK_ADD_PAYMENT'));
            if ($item->status === '1' || $item->status === '2') {
                $url = JRoute::_("index.php?option={$this->option}&amp;view=payments&amp;scoreID={$item->id}");
                $arr['payments_link'] = JHtml::link($url, JText::sprintf('COM_FINANCES_LINK_PAYMENTS'));
            }
            if (is_null($item->status)) {
                $url = JRoute::_("index.php?option={$this->option}&amp;task=score.add&amp;contractID={$item->contractID}&amp;return={$return}");
                $arr['payment_link'] = JHtml::link($url, JText::sprintf('COM_MKV_BUTTON_ADD_SCORE'));
            }
            $result['items'][] = $arr;
        }
        $result['amount'] = FinancesHelper::getProjectScoresAmount();
        $result['contracts_amount'] = ContractsHelper::getProjectAmount();
        return $result;
    }

    public function getFilterForm($data = array(), $loadData = true)
    {
        $form = parent::getFilterForm($data, $loadData);
        $form::addFieldPath(JPATH_ADMINISTRATOR . "/components/com_contracts/models/fields");
        return $form;
    }

    public function getTitle(): string
    {
        if ($this->contractID > 0) {
            $contract = $this->getContract($this->contractID);
            return JText::sprintf('COM_FINANCES_TITLE_SCORES_BY_CONTRACT', $contract->company, $contract->project);
        }
        else {
            return JText::sprintf('COM_FINANCES_TITLE_SCORES');
        }
    }

    public function getContractID()
    {
        return $this->contractID;
    }

    private function getContract(int $contractID)
    {
        JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . "/components/com_contracts/models");
        JTable::addIncludePath(JPATH_ADMINISTRATOR . "/components/com_contracts/tables");
        $model = JModelLegacy::getInstance('Contract', 'ContractsModel');
        return $model->getItem($contractID);
    }

    /* Сортировка по умолчанию */
    protected function populateState($ordering = 's.id', $direction = 'DESC')
    {
        $contract_status = $this->getUserStateFromRequest($this->context . '.filter.contract_status', 'filter_contract_status');
        $this->setState('filter.contract_status', $contract_status);
        $status = $this->getUserStateFromRequest($this->context . '.filter.status', 'filter_status', '', 'string');
        $this->setState('filter.status', $status);
        $manager = $this->getUserStateFromRequest($this->context . '.filter.manager', 'filter_manager');
        $this->setState('filter.manager', $manager);
        parent::populateState($ordering, $direction);
        PrjHelper::check_refresh();
    }

    protected function getStoreId($id = '')
    {
        $id .= ':' . $this->getState('filter.contract_status');
        $id .= ':' . $this->getState('filter.status');
        $id .= ':' . $this->getState('filter.manager');
        return parent::getStoreId($id);
    }

    private $contractID, $raw;
}