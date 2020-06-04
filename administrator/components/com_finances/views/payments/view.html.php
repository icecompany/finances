<?php
use Joomla\CMS\MVC\View\HtmlView;

defined('_JEXEC') or die;

class FinancesViewPayments extends HtmlView
{
    protected $sidebar = '';
    public $items, $pagination, $uid, $state, $filterForm, $activeFilters, $scoreID;

    public function display($tpl = null)
    {
        $this->items = $this->get('Items');
        $this->pagination = $this->get('Pagination');
        $this->state = $this->get('State');
        $this->filterForm = $this->get('FilterForm');
        $this->activeFilters = $this->get('ActiveFilters');
        $this->scoreID = $this->get('ScoreID');

        $this->filterForm->addFieldPath(JPATH_ADMINISTRATOR . "/components/com_mkv/models/fields");

        // Show the toolbar
        $this->toolbar();

        // Show the sidebar
        FinancesHelper::addSubmenu('payments');
        $this->sidebar = JHtmlSidebar::render();

        // Display it all
        return parent::display($tpl);
    }

    private function toolbar()
    {
        JToolBarHelper::title($this->get('Title'), 'credit');

        if ($this->scoreID > 0)
        {
            JToolbarHelper::custom('payments.reset', 'arrow-left', 'arrow-left', JText::sprintf('COM_FINANCES_BUTTON_GOTO_ALL_PAYMENTS'), false);
        }
        if ($this->scoreID > 0 && FinancesHelper::canDo('core.add'))
        {
            JToolbarHelper::addNew('payment.add');
        }
        if (FinancesHelper::canDo('core.edit'))
        {
            JToolbarHelper::editList('payment.edit');
        }
        if (FinancesHelper::canDo('core.delete'))
        {
            JToolbarHelper::deleteList('COM_FINANCES_CONFIRM_REMOVE_PAYMENT', 'payments.delete');
        }

        if (FinancesHelper::canDo('core.admin'))
        {
            JToolBarHelper::preferences('com_finances');
        }
    }
}
