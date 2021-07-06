<?php
use Joomla\CMS\MVC\Controller\AdminController;

defined('_JEXEC') or die;

class FinancesControllerScores extends AdminController
{
    public function getModel($name = 'Scores', $prefix = 'FinancesModel', $config = ['raw' => true])
    {
        return parent::getModel($name, $prefix, $config);
    }

    public function execute($task): void
    {
        $model = $this->getModel();
        $model->export();
        jexit();
    }

}
