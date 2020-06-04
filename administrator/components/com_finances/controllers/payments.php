<?php
use Joomla\CMS\MVC\Controller\AdminController;
defined('_JEXEC') or die;

class FinancesControllerPayments extends AdminController
{
    public function reset()
    {
        $this->setRedirect("index.php?option=com_finances&view=payments");
        $this->redirect();
        jexit();
    }

    public function getModel($name = 'Payment', $prefix = 'FinancesModel', $config = array())
    {
        return parent::getModel($name, $prefix, array('ignore_request' => true));
    }
}
