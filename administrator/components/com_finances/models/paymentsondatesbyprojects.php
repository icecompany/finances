<?php
defined('_JEXEC') or die;
use Joomla\CMS\MVC\Model\ListModel;

class FinancesModelPaymentsOnDatesByProjects extends ListModel
{
    public function __construct(array $config)
    {
        if (empty($config['filter_fields']))
        {
            $config['filter_fields'] = array(
                'p.id',
            );
        }
        parent::__construct($config);
        $this->date_1 = JDate::getInstance($config['date_1'])->format("Y-m-d");
        $this->date_2 = JDate::getInstance($config['date_2'])->format("Y-m-d");
        $this->project_1 = (int) $config['project_1'] ?? 0;
        $this->project_2 = (int) $config['project_2'] ?? 0;
    }

    protected function _getListQuery()
    {
        $query = $this->_db->getQuery(true);
        $query
            ->select("c.managerID, c.projectID, c.currency")
            ->select("sum(p.amount) as payments")
            ->from("#__mkv_payments p")
            ->leftJoin("#__mkv_scores s on s.id = p.scoreID")
            ->leftJoin("#__mkv_contracts c on c.id = s.contractID")
            ->leftJoin("#__users u on c.managerID = u.id")
            ->group("c.managerID, c.projectID, c.currency");

        $date_1 = $this->_db->q($this->date_1);
        $date_2 = $this->_db->q($this->date_2);
        $project_1 = $this->_db->q($this->project_1);
        $project_2 = $this->_db->q($this->project_2);
        $query->where("((c.projectID = {$project_1} and p.dat <= {$date_1}) or (c.projectID = {$project_2} and p.dat <= {$date_2}))");

        $this->setState('list.limit', 0);

        return $query;
    }

    public function getItems()
    {
        $items = parent::getItems();
        $result = [];
        foreach ($items as $item)
        {
            if (!isset($result[$item->managerID])) {
                foreach ([$this->project_1, $this->project_2] as $project) {
                    foreach (['rub', 'usd', 'eur'] as $currency) {
                        $result[$item->managerID][$project][$currency] = 0;
                    }
                }
            }
            $result[$item->managerID][$item->projectID][$item->currency] = (float) $item->payments;
        }

        return $result;
    }

    /* Сортировка по умолчанию */
    protected function populateState($ordering = 'p.id', $direction = 'ASC')
    {
        parent::populateState($ordering, $direction);
    }

    protected function getStoreId($id = '')
    {
        return parent::getStoreId($id);
    }

    private $date_1, $date_2, $project_1, $project_2;
}