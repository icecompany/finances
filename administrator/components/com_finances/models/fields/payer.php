<?php
defined('_JEXEC') or die;
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

class JFormFieldPayer extends JFormFieldList
{
    protected $type = 'Payer';
    protected $loadExternally = 0;

    protected function getOptions()
    {
        $id = JFactory::getApplication()->input->getInt('id', null);
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        if ($id !== null) {
            $query->select("e.id, e.title")
                ->from("#__mkv_payments p")
                ->leftJoin("#__mkv_scores s on s.id = p.scoreID")
                ->leftJoin("#__mkv_contract_items ci on ci.contractID = s.contractID")
                ->leftJoin("#__mkv_companies e on e.id = ci.payerID")
                ->where("p.id = {$id}");
        }
        else {
            $scoreID = JFactory::getApplication()->getUserState('com_finances.payment.scoreID');
            $query->select("e.id, e.title")
                ->from("#__mkv_scores s")
                ->leftJoin("#__mkv_contract_items ci on ci.contractID = s.contractID")
                ->leftJoin("#__mkv_companies e on e.id = ci.payerID")
                ->where("s.id = {$scoreID}");
        }
        $result = $db->setQuery($query)->loadObjectList();

        $options = array();

        foreach ($result as $item) {
            $options[] = JHtml::_('select.option', $item->id, $item->title);
        }

        if (!$this->loadExternally) {
            $options = array_merge(parent::getOptions(), $options);
        }

        return $options;
    }

    public function getOptionsExternally()
    {
        $this->loadExternally = 1;
        return $this->getOptions();
    }
}