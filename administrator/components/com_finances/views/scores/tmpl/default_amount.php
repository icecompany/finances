<?php
// Запрет прямого доступа.
defined('_JEXEC') or die;
$colspan = 7;
?>
<tr>
    <td colspan="<?php echo $colspan;?>" rowspan="3" style="text-align: right">
        <?php echo JText::sprintf('COM_CONTRACTS_HEAD_TOTAL_AMOUNT_BY_PROJECT'); ?>
    </td>
    <td><?php echo $this->items['contracts_amount']['rub']['amount'];?></td>
    <td><?php echo $this->items['amount']['rub'];?></td>
    <td><?php echo $this->items['contracts_amount']['rub']['payments'];?></td>
    <td colspan="5"><?php echo $this->items['contracts_amount']['rub']['debt'];?></td>
</tr>
<tr>
    <td><?php echo $this->items['contracts_amount']['usd']['amount'];?></td>
    <td><?php echo $this->items['amount']['usd'];?></td>
    <td><?php echo $this->items['contracts_amount']['usd']['payments'];?></td>
    <td colspan="5"><?php echo $this->items['contracts_amount']['usd']['debt'];?></td>
</tr>
<tr>
    <td><?php echo $this->items['contracts_amount']['eur']['amount'];?></td>
    <td><?php echo $this->items['amount']['eur'];?></td>
    <td><?php echo $this->items['contracts_amount']['eur']['payments'];?></td>
    <td colspan="5"><?php echo $this->items['contracts_amount']['eur']['debt'];?></td>
</tr>
