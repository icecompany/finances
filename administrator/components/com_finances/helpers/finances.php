<?php
use Joomla\CMS\HTML\HTMLHelper;

defined('_JEXEC') or die;

class FinancesHelper
{
    public static function addSubmenu($vName)
    {
        PrjHelper::addNotifies();
        HTMLHelper::_('sidebar.addEntry', JText::sprintf('COM_FINANCES_MENU_SCORES'), 'index.php?option=com_finances&view=scores', $vName === 'scores');
        HTMLHelper::_('sidebar.addEntry', JText::sprintf('COM_FINANCES_MENU_PAYMENTS'), 'index.php?option=com_finances&view=payments', $vName === 'payments');
        PrjHelper::addActiveProjectFilter();
    }
    public static function getPaymentStatusColor($status)
    {
        switch ($status) {
            case null: {
                return 'orange';
            }
            case 0: {
                return 'red';
            }
            case 1: {
                return 'green';
            }
            default: return 'blue';
        }
	}

    public static function getProjectScoresAmount(): array
    {
        $result = ['rub' => 0, 'usd' => 0, 'eur' => 0];
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query
            ->select("c.currency, sum(sc.amount) as amount")
            ->from("#__mkv_scores sc")
            ->leftJoin("#__mkv_contracts c on sc.contractID = c.id")
            ->group("c.currency");

        $active_project = PrjHelper::getActiveProject();
        if (is_numeric($active_project)) $query->where("c.projectID = {$db->q($active_project)}");

        $items = $db->setQuery($query)->loadAssocList();
        foreach ($items as $item) $result[$item['currency']] = $item['amount'];
        foreach ($result as $currency => $amount) {
            $currency_up = mb_strtoupper($currency);
            $result[$currency] = JText::sprintf("COM_MKV_AMOUNT_{$currency_up}_SHORT", number_format((float) $amount, MKV_FORMAT_DEC_COUNT, MKV_FORMAT_SEPARATOR_FRACTION, MKV_FORMAT_SEPARATOR_DEC));
        }
        return $result;
	}

    public static function getActionUrl(): string
    {
        $uri = JUri::getInstance();
        $uri->setVar('refresh', '1');
        $input = JFactory::getApplication()->input;
        $view = $input->getString('view');
        $contractID = $input->getInt('contractID', 0);
        $scoreID = $input->getInt('scoreID', 0);
        if (($view === 'scores' && $contractID > 0) || ($view === 'payments' && $scoreID > 0)) {
            $uri->setVar('return', self::getReturnUrl());
        }
        $query = $uri->getQuery();
        $client = (!JFactory::getApplication()->isClient('administrator')) ? 'site' : 'administrator';
        return JRoute::link($client, "index.php?{$query}");
    }

    public static function getReturnUrl(): string
    {
        $uri = JUri::getInstance();
        $query = $uri->getQuery();
        return base64_encode("index.php?{$query}");
    }

    public static function canDo(string $action): bool
    {
        return JFactory::getUser()->authorise($action, 'com_finances');
    }

    public static function getConfig(string $param, $default = null)
    {
        $config = JComponentHelper::getParams("com_finances");
        return $config->get($param, $default);
    }
}
