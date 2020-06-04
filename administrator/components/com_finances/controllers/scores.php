<?php
use Joomla\CMS\MVC\Controller\AdminController;
defined('_JEXEC') or die;

class FinancesControllerScores extends AdminController
{
    public function getModel($name = 'Score', $prefix = 'FinancesModel', $config = array())
    {
        return parent::getModel($name, $prefix, array('ignore_request' => true));
    }

    public function reset()
    {
        $this->setRedirect("index.php?option=com_finances&view=scores");
        $this->redirect();
        jexit();
    }
}
