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

    public static function canDo(string $action): bool
    {
        return JFactory::getUser()->authorise($action, 'com_finances');
    }
}
