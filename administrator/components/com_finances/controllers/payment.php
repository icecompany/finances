<?php
defined('_JEXEC') or die;
use Joomla\CMS\MVC\Controller\FormController;

class FinancesControllerPayment extends FormController {
    public function add()
    {
        $uri = JUri::getInstance();
        $scoreID = $uri->getVar('scoreID', 0);
        $referer = JUri::getInstance($_SERVER['HTTP_REFERER']);
        if ($referer->getVar('view') === 'score') {
            $scoreID = $referer->getVar('id');
            $this->input->set('return', base64_encode($_SERVER['HTTP_REFERER']));
        }
        if ($referer->getVar('view') === 'payments') {
            $scoreID = $uri->getVar('scoreID');
            $this->input->set('return', base64_encode($uri->getVar('return')));
        }
        if ($scoreID > 0) JFactory::getApplication()->setUserState($this->option . '.payment.scoreID', $scoreID);
        return parent::add();
    }

    public function display($cachable = false, $urlparams = array())
    {
        return parent::display($cachable, $urlparams);
    }
}