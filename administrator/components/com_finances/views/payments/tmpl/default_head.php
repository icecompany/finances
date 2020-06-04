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
        <?php echo JText::sprintf('COM_MKV_HEAD_STANDS'); ?>
    </th>
    <th>
        <?php echo JHtml::_('searchtools.sort', 'COM_MKV_HEAD_PAYMENT_ORDER', 'p.order_name', $listDirn, $listOrder); ?>
    </th>
    <th>
        <?php echo JHtml::_('searchtools.sort', 'COM_MKV_HEAD_PAYMENT_DATE', 'p.dat', $listDirn, $listOrder); ?>
    </th>
    <th>
        <?php echo JHtml::_('searchtools.sort', 'COM_MKV_HEAD_AMOUNT', 'p.amount', $listDirn, $listOrder); ?>
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
        <?php echo JHtml::_('searchtools.sort', 'COM_MKV_HEAD_PAYER', 'payer', $listDirn, $listOrder); ?>
    </th>
    <th style="width: 1%;">
        <?php echo JHtml::_('searchtools.sort', 'ID', 'p.id', $listDirn, $listOrder); ?>
    </th>
</tr>
