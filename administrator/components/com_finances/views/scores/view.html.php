<?php
use Joomla\CMS\MVC\View\HtmlView;

defined('_JEXEC') or die;

class FinancesViewScores extends HtmlView
{
    protected $sidebar = '';
    public $items, $pagination, $uid, $state, $filterForm, $activeFilters, $contractID;

    public function display($tpl = null)
    {
        $this->items = $this->get('Items');
        $this->pagination = $this->get('Pagination');
        $this->state = $this->get('State');
        $this->filterForm = $this->get('FilterForm');
        $this->activeFilters = $this->get('ActiveFilters');
        $this->contractID = $this->get('ContractID');

        $this->filterForm->addFieldPath(JPATH_ADMINISTRATOR . "/components/com_mkv/models/fields");

        // Show the toolbar
        $this->toolbar();

        // Show the sidebar
        FinancesHelper::addSubmenu('scores');
        $this->sidebar = JHtmlSidebar::render();

        // Display it all
        return parent::display($tpl);
    }

    private function toolbar()
    {
        JToolBarHelper::title($this->get('Title'), 'credit');

        if ($this->contractID > 0)
        {
            JToolbarHelper::custom('scores.reset', 'arrow-left', 'arrow-left', JText::sprintf('COM_FINANCES_BUTTON_GOTO_ALL_SCORES'), false);
        }
        if ($this->contractID > 0 && FinancesHelper::canDo('core.add'))
        {
            JToolbarHelper::addNew('score.add');
        }
        if (FinancesHelper::canDo('core.edit'))
        {
            JToolbarHelper::editList('score.edit');
        }
        if (FinancesHelper::canDo('core.delete'))
        {
            JToolbarHelper::deleteList('COM_FINANCES_CONFIRM_REMOVE_SCORE', 'scores.delete');
        }

        if (FinancesHelper::canDo('core.admin'))
        {
            JToolBarHelper::preferences('com_finances');
        }
    }
}
