<?php
/**
 * @package    finances
 *
 * @author     anton@nazvezde.ru <your@email.com>
 * @copyright  A copyright
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       http://your.url.com
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Layout\FileLayout;

/** @var FinancesViewFinances $this */

HTMLHelper::_('script', 'com_finances/script.js', ['version' => 'auto', 'relative' => true]);
HTMLHelper::_('stylesheet', 'com_finances/style.css', ['version' => 'auto', 'relative' => true]);

$layout       = new FileLayout('finances.page');
$data         = [];
$data['text'] = 'Hello Joomla!';
echo $layout->render($data);
