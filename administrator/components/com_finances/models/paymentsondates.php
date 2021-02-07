<?php
defined('_JEXEC') or die;
use Joomla\CMS\MVC\Model\ListModel;

class FinancesModelPaymentsOnDates extends ListModel
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
        $this->projectID = (int) $config['projectID'] ?? 0;
    }

    protected function _getListQuery()
    {
        $query = $this->_db->getQuery(true);
        $date_1 = $this->_db->q($this->date_1);
        $query
            ->select("c.managerID, c.currency, if(p.dat > {$date_1}, 'current', 'week') as period, sum(p.amount) as payments")
            ->from("#__mkv_payments p")
            ->leftJoin("#__mkv_scores s on s.id = p.scoreID")
            ->leftJoin("#__mkv_contracts c on c.id = s.contractID")
            ->leftJoin("#__users u on c.managerID = u.id")
            ->group("c.managerID, period, c.currency");

        $date_2 = $this->_db->q($this->date_2);
        $projectID = $this->_db->q($this->projectID);
        $query->where("(c.projectID = {$projectID} and p.dat < {$date_2})");

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
                foreach (['current', 'week', 'dynamic'] as $period) {
                    foreach (['rub', 'usd', 'eur'] as $currency) {
                        $result[$item->managerID][$period][$currency] = (float) 0;
                    }
                }
            }
            $result[$item->managerID][$item->period][$item->currency] = (float) $item->payments;
            foreach (['rub', 'usd', 'eur'] as $currency) {
                $result[$item->managerID]['dynamic'][$currency] = (float) $result[$item->managerID]['current'][$currency];
            }
        }
        foreach ($result as $managerID => $data) {
            foreach (['rub', 'usd', 'eur'] as $currency) {
                $result[$managerID]['current'][$currency] = (float) $data['week'][$currency] + (float) $data['dynamic'][$currency];
            }
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

    private $date_1, $date_2, $projectID;
}