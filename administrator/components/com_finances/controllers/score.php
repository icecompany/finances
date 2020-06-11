<?php
defined('_JEXEC') or die;
use Joomla\CMS\MVC\Controller\FormController;

class FinancesControllerScore extends FormController {
    public function add()
    {
        $uri = JUri::getInstance();
        $contractID = $uri->getVar('contractID', 0);
        $referer = JUri::getInstance($_SERVER['HTTP_REFERER']);
        if ($referer->getVar('view') === 'contract') {
            $contractID = $referer->getVar('id');
            $this->input->set('return', base64_encode(base64_encode($referer->toString())));
        }
        if ($referer->getVar('view') === 'scores') {
            $contractID = $referer->getVar('contractID');
            $this->input->set('return', base64_encode($referer->toString()));
        }
        if ($referer->getVar('view') === 'contracts') {
            $contractID = $uri->getVar('contractID');
            $this->input->set('return', $uri->getVar('return'));
        }
        if ($contractID > 0) JFactory::getApplication()->setUserState($this->option . '.score.contractID', $contractID);
        return parent::add();
    }

    public function display($cachable = false, $urlparams = array())
    {
        return parent::display($cachable, $urlparams);
    }
}