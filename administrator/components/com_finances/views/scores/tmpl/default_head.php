<?php
defined('_JEXEC') or die;
$listOrder    = $this->escape($this->state->get('list.ordering'));
$listDirn    = $this->escape($this->state->get('list.direction'));
?>
<tr>
    <th style="width: 1%;">
        <?php echo JHtml::_('grid.checkall'); ?>
    </th>
    <th style="width: 1%;">
        №
    </th>
    <th>
        <?php echo JHtml::_('searchtools.sort', 'COM_MKV_HEAD_SCORE_NUMBER', 's.number', $listDirn, $listOrder); ?>
    </th>
    <th>
        <?php echo JHtml::_('searchtools.sort', 'COM_MKV_HEAD_SCORE_DATE', 's.dat', $listDirn, $listOrder); ?>
    </th>
    <th>
        <?php echo JHtml::_('searchtools.sort', 'COM_MKV_HEAD_CONTRACT_NUMBER', 'contract_number', $listDirn, $listOrder); ?>
    </th>
    <th>
        <?php echo JHtml::_('searchtools.sort', 'COM_MKV_HEAD_CONTRACT_DATE', 'c.dat', $listDirn, $listOrder); ?>
    </th>
    <th>
        <?php echo JHtml::_('searchtools.sort', 'COM_MKV_HEAD_COMPANY', 'e.title', $listDirn, $listOrder); ?>
    </th>
    <th>
        <?php echo JHtml::_('searchtools.sort', 'COM_MKV_HEAD_CONTRACT_AMOUNT', 'c.amount', $listDirn, $listOrder); ?>
    </th>
    <th>
        <?php echo JHtml::_('searchtools.sort', 'COM_MKV_HEAD_PAYMENT_AMOUNT', 's.amount', $listDirn, $listOrder); ?>
    </th>
    <th>
        <?php echo JHtml::_('searchtools.sort', 'COM_MKV_HEAD_PAYED', 's.payments', $listDirn, $listOrder); ?>
    </th>
    <th>
        <?php echo JHtml::_('searchtools.sort', 'COM_MKV_HEAD_DEBT', 's.debt', $listDirn, $listOrder); ?>
    </th>
    <th>
        <?php echo JText::sprintf('COM_MKV_HEAD_PAYMENTS'); ?>
    </th>
    <th>
        <?php echo JText::sprintf('COM_MKV_HEAD_ADD_PAYMENTS'); ?>
    </th>
    <th>
        <?php echo JHtml::_('searchtools.sort', 'COM_MKV_HEAD_STATUS', 's.status', $listDirn, $listOrder); ?>
    </th>
    <th style="width: 1%;">
        <?php echo JHtml::_('searchtools.sort', 'ID', 's.id', $listDirn, $listOrder); ?>
    </th>
</tr>
