<?php
use Joomla\CMS\HTML\HTMLHelper;

defined('_JEXEC') or die;

class FinancesHelper
{
    public static function addSubmenu($vName)
    {
        HTMLHelper::_('sidebar.addEntry', JText::sprintf('COM_FINANCES_MENU_SCORES'), 'index.php?option=com_finances&view=scores', $vName === 'scores');
        HTMLHelper::_('sidebar.addEntry', JText::sprintf('COM_FINANCES_MENU_PAYMENTS'), 'index.php?option=com_finances&view=payments', $vName === 'payments');
        PrjHelper::addActiveProjectFilter();
    }
    public static function getPaymentStatusColor(int $status)
    {
        switch ($status) {
            case 0: {
                return 'red';
                break;
            }
            case 1: {
                return 'green';
                break;
            }
            default: return 'blue';
        }
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
