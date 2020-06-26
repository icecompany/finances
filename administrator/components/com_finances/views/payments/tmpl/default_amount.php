<?php
// Запрет прямого доступа.
defined('_JEXEC') or die;
$colspan = 5;
?>
<tr>
    <td colspan="<?php echo $colspan;?>" rowspan="3" style="text-align: right;">
        <?php echo JText::sprintf('COM_FINANCES_TOTAL_PAYMENTS_BY_PROJECT'); ?>
    </td>
    <td colspan="8"><?php echo $this->items['amount']['rub']['payments'];?></td>
</tr>
<tr>
    <td colspan="8"><?php echo $this->items['amount']['usd']['payments'];?></td>
</tr>
<tr>
    <td colspan="8"><?php echo $this->items['amount']['eur']['payments'];?></td>
</tr>
