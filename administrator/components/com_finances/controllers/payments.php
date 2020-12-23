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

    public function download(): void
    {
        echo "<script>window.open('index.php?option=com_finances&task=payments.execute&format=xls');</script>";
        echo "<script>location.href='{$_SERVER['HTTP_REFERER']}'</script>";
        jexit();
    }
}
