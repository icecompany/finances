<?php
defined('_JEXEC') or die;
use Joomla\CMS\MVC\View\HtmlView;

class FinancesViewPayment extends HtmlView {
    protected $item, $form, $script;

    public function display($tmp = null) {
        $this->form = $this->get('Form');
        $this->item = $this->get('Item');
        $this->script = $this->get('Script');

        $this->addToolbar();
        $this->setDocument();

        parent::display($tmp);
    }

    protected function addToolbar() {
	    JToolBarHelper::apply('payment.apply', 'JTOOLBAR_APPLY');
        JToolbarHelper::save('payment.save', 'JTOOLBAR_SAVE');
        JToolbarHelper::cancel('payment.cancel', 'JTOOLBAR_CLOSE');
        JFactory::getApplication()->input->set('hidemainmenu', true);
    }

    protected function setDocument() {
        JToolbarHelper::title($this->item->title, 'pencil-2');
        JHtml::_('bootstrap.framework');
    }
}