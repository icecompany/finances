<?php
defined('_JEXEC') or die;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\MVC\Model\AdminModel;

class FinancesModelPayments extends ListModel
{
    public function __construct(array $config)
    {
        if (empty($config['filter_fields']))
        {
            $config['filter_fields'] = array(
                'p.id',
                's.dat',
                's.number',
                'contract_number',
                's.amount',
                'p.amount',
                'payer',
                'manager',
                'search',
                'e.title',
                'p.order_name',
                'p.dat',
                'c.dat',
            );
        }
        parent::__construct($config);
        $this->raw = false;
        $input = JFactory::getApplication()->input;
        if ($input->getInt('scoreID', 0) > 0) {
            $this->scoreID = $input->getInt('scoreID');
        }
        if (isset($config['scoreID'])) {
            $this->scoreID = $config['scoreID'];
            $this->raw = true;
        }
        if (isset($config['contractID'])) {
            $this->contractID = $config['contractID'];
            $this->raw = true;
        }
        $this->export = ($input->getString('format', 'html') !== 'html');
        $this->heads = [
            'stands' => 'COM_MKV_HEAD_STANDS',
            'order_name' => 'COM_MKV_HEAD_PAYMENT_ORDER',
            'dat' => 'COM_MKV_HEAD_PAYMENT_DATE',
            'amount_export' => 'COM_MKV_HEAD_AMOUNT',
            'score_number' => 'COM_MKV_HEAD_SCORE_NUMBER',
            'score_date' => 'COM_MKV_HEAD_SCORE_DATE',
            'contract_number_clean' => 'COM_MKV_HEAD_CONTRACT_NUMBER',
            'contract_date' => 'COM_MKV_HEAD_CONTRACT_DATE',
            'company' => 'COM_MKV_HEAD_COMPANY',
            'manager' => 'COM_MKV_HEAD_MANAGER',
        ];
    }

    protected function _getListQuery()
    {
        $query = $this->_db->getQuery(true);
        $query
            ->select("e.title as company")
            ->select("e1.title as payer")
            ->select("p.*")
            ->select("ifnull(c.number_free, c.number) as contract_number, c.dat as contract_date")
            ->select("c.currency, c.companyID")
            ->select("s.dat as score_dat, s.number as score_number, s.contractID, s.status as score_status, s.amount as score_amount")
            ->from("#__mkv_payments p")
            ->leftJoin("#__mkv_scores s on s.id = p.scoreID")
            ->leftJoin("#__mkv_contracts c on c.id = s.contractID")
            ->leftJoin("#__mkv_companies e on e.id = c.companyID")
            ->leftJoin("#__mkv_companies e1 on e1.id = p.payerID");
        if ($this->export) {
            $query
                ->select("u.name as manager")
                ->leftJoin("#__users u on u.id = c.managerID");
        }
        if (is_numeric($this->scoreID)) {
            $query->where("p.scoreID = {$this->_db->q($this->scoreID)}");
        }
        if (is_numeric($this->contractID) && $this->raw) {
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
                    $query->where("(e.title like {$text} or e.title_full like {$text} or e.title_en like {$text} or e1.title like {$text} or e1.title_full like {$text} or e1.title_en like {$text})");
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
            /* Сортировка */
            $orderCol  = $this->state->get('list.ordering');
            $orderDirn = $this->state->get('list.direction');
            $limit = $this->getState('list.limit');
            if ($orderCol == 'p.order_name') {
                if ($orderDirn == 'ASC') $orderCol = "LENGTH(p.order_name) ASC, p.order_name";
                if ($orderDirn == 'DESC') $orderCol = "LENGTH(p.order_name) DESC, p.order_name";
            }
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
        if ($this->export) $limit = 0;
        $query->order($this->_db->escape($orderCol . ' ' . $orderDirn));
        $this->setState('list.limit', $limit);

        return $query;
    }

    public function getItems()
    {
        $items = parent::getItems();
        $result = ['items' => [], 'stands' => [], 'amount'];
        $return = PrjHelper::getReturnUrl();
        $cid = [];
        foreach ($items as $item)
        {
            $arr = [];
            $arr['id'] = $item->id;
            if (array_search($item->contractID, $cid) === false) $cid[] = $item->contractID;
            $arr['number'] = $item->number;
            $arr['company'] = $item->company;
            $arr['order_name'] = $item->order_name;
            $arr['score_number'] = $item->score_number;
            $arr['contractID'] = $item->contractID;
            $arr['payer'] = $item->payer;
            $arr['payerID'] = $item->payerID;
            $currency = mb_strtoupper($item->currency);
            $arr['score_date'] = JDate::getInstance($item->score_dat)->format("d.m.Y");
            $arr['contract'] = MkvHelper::getContractSmallTitle($item->contract_number ?? '', '');
            $arr['contract_number_clean'] = $item->contract_number;
            $arr['score_status_clean'] = JText::sprintf("COM_MKV_PAYMENT_STATUS_{$item->score_status}");
            $arr['color'] = FinancesHelper::getPaymentStatusColor($item->score_status);
            $arr['score_status'] = "<span style='color: {$arr['color']}'>{$arr['score_status_clean']}</span>";
            $score_amount = number_format((float) $item->score_amount, MKV_FORMAT_DEC_COUNT, MKV_FORMAT_SEPARATOR_FRACTION, MKV_FORMAT_SEPARATOR_DEC);
            $arr['score_amount'] = JText::sprintf("COM_MKV_AMOUNT_{$currency}_SHORT", $score_amount);
            $arr['dat'] = JDate::getInstance($item->dat)->format("d.m.Y");
            $arr['contract_date'] = JDate::getInstance($item->contract_date)->format("d.m.Y");
            $arr['amount_clean'] = $item->amount;
            $arr['amount_export'] = number_format((float) $item->amount, MKV_FORMAT_DEC_COUNT, MKV_FORMAT_SEPARATOR_FRACTION, '');
            $amount = number_format((float) $item->amount, MKV_FORMAT_DEC_COUNT, MKV_FORMAT_SEPARATOR_FRACTION, MKV_FORMAT_SEPARATOR_DEC);
            $arr['amount'] = JText::sprintf("COM_MKV_AMOUNT_{$currency}_SHORT", $amount);
            $url = JRoute::_("index.php?option={$this->option}&amp;task=payment.edit&amp;id={$item->id}&amp;return={$return}");
            $arr['edit_link'] = JHtml::link($url, $item->order_name);
            $url = JRoute::_("index.php?option=com_companies&amp;task=company.edit&amp;id={$item->companyID}&amp;return={$return}");
            $arr['company_link'] = JHtml::link($url, $item->company);
            $url = JRoute::_("index.php?option=com_companies&amp;task=company.edit&amp;id={$item->payerID}&amp;return={$return}");
            $arr['payer_link'] = JHtml::link($url, $item->payer);
            $url = JRoute::_("index.php?option=com_contracts&amp;task=contract.edit&amp;id={$item->contractID}&amp;return={$return}");
            $arr['contract_link'] = JHtml::link($url, $arr['contract']);
            $url = JRoute::_("index.php?option={$this->option}&amp;task=score.edit&amp;id={$item->scoreID}&amp;return={$return}");
            $arr['score_link'] = JHtml::link($url, $arr['score_number']);
            if ($this->export) $arr['manager'] = MkvHelper::getLastAndFirstNames($item->manager);
            $result['items'][] = $arr;
        }
        $project = PrjHelper::getActiveProject();
        if (is_numeric($project) && ContractsHelper::canDo('core.project.amount')) $result['amount'] = ContractsHelper::getProjectAmount((int) $project);
        $result['stands'] = $this->getStands($cid);

        return $result;
    }

    public function export()
    {
        $items = $this->getItems();
        JLoader::discover('PHPExcel', JPATH_LIBRARIES);
        JLoader::register('PHPExcel', JPATH_LIBRARIES . '/PHPExcel.php');

        $xls = new PHPExcel();
        $xls->setActiveSheetIndex(0);
        $sheet = $xls->getActiveSheet();

        //Ширина столбцов
        $width = ["A" => 19, "B" => 8, "C" => 14, "D" => 12, "E" => 13, "F" => 11, "G" => 13, "H" => 15, "I" => 72, "J" => 20];
        foreach ($width as $col => $value) $sheet->getColumnDimension($col)->setWidth($value);

        //Заголовки
        $j = 0;
        foreach ($this->heads as $item => $head) $sheet->setCellValueByColumnAndRow($j++, 1, JText::sprintf($head));

        $sheet->setTitle(JText::sprintf('COM_FINANCES_MENU_PAYMENTS'));

        //Данные
        $row = 2; //Строка, с которой начнаются данные
        foreach ($items['items'] as $item) {
            $col = 0;
            foreach ($this->heads as $elem => $head) {
                if ($col === 0) {
                    $sheet->setCellValue("A{$row}", $items['stands'][$item['contractID']]);
                }
                else {
                    $sheet->setCellValueByColumnAndRow($col, $row, $item[$elem]);
                    $sheet->getStyleByColumnAndRow($col, $row)->getNumberFormat()->setFormatCode('0');
                }
                $col++;
            }
            $row++;
        }
        header("Expires: Mon, 1 Apr 1974 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: public");
        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=Payments.xls");
        $objWriter = PHPExcel_IOFactory::createWriter($xls, 'Excel5');
        $objWriter->save('php://output');
        jexit();
    }

    public function getTitle(): string
    {
        if ($this->scoreID > 0) {
            $score = $this->getScore($this->scoreID);
            $contract = $this->getContract($score->contractID);
            return JText::sprintf('COM_FINANCES_TITLE_PAYMENTS_BY_SCORE', $score->number, $contract->company, $contract->project);
        }
        else {
            return JText::sprintf('COM_FINANCES_TITLE_PAYMENTS');
        }
    }

    public function getScoreID()
    {
        return $this->scoreID;
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

    private function getStands(array $ids = []): array
    {
        if (empty($ids)) return [];
        JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . "/components/com_contracts/models", "ContractsModel");
        $model = JModelLegacy::getInstance('StandsLight', 'ContractsModel', ['contractIDs' => $ids, 'byContractID' => true, 'byCompanyID' => false]);
        $items = $model->getItems();
        $result = [];
        $tmp = [];
        foreach ($items as $contractID => $data) {
            foreach ($data as $item) $tmp[$contractID][] = (!$this->export) ? $item['edit_link'] : $item['number'];
        }
        foreach ($tmp as $contractID => $stand) $result[$contractID] = implode((!$this->export) ? '<br>' : ', ', $stand);
        return $result;
    }

    /* Сортировка по умолчанию */
    protected function populateState($ordering = 'p.id', $direction = 'DESC')
    {
        $manager = $this->getUserStateFromRequest($this->context . '.filter.manager', 'filter_manager');
        $this->setState('filter.manager', $manager);
        parent::populateState($ordering, $direction);
        PrjHelper::check_refresh();
    }

    protected function getStoreId($id = '')
    {
        $id .= ':' . $this->getState('filter.manager');
        return parent::getStoreId($id);
    }

    private $scoreID, $raw, $contractID, $heads, $export;
}