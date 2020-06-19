<?php
defined('_JEXEC') or die;
use Joomla\CMS\MVC\Controller\BaseController;

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_finances'))
{
	throw new InvalidArgumentException(JText::sprintf('JERROR_ALERTNOAUTHOR'), 404);
}

// Require the helper
JFactory::getLanguage()->load('com_mkv', JPATH_ADMINISTRATOR . "/components/com_mkv", 'ru-RU', true);
require_once JPATH_ADMINISTRATOR . "/components/com_mkv/helpers/mkv.php";
require_once JPATH_ADMINISTRATOR . "/components/com_prj/helpers/prj.php";
require_once JPATH_ADMINISTRATOR . "/components/com_contracts/helpers/contracts.php";
require_once JPATH_ADMINISTRATOR . "/components/com_scheduler/helpers/scheduler.php";
require_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/finances.php';

//Enable triggers
$db = JFactory::getDbo();

// Execute the task
$controller = BaseController::getInstance('finances');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
