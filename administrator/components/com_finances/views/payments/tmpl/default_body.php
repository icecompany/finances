<?php
// Запрет прямого доступа.
defined('_JEXEC') or die;
$ii = $this->state->get('list.start', 0);
foreach ($this->items['items'] as $i => $item) :
    ?>
    <tr class="row<?php echo $i % 2; ?>">
        <td class="center">
            <?php echo JHtml::_('grid.id', $i, $item['id']); ?>
        </td>
        <td>
            <?php echo ++$ii; ?>
        </td>
        <td>
            <?php echo $this->items['stands'][$item['contractID']]; ?>
        </td>
        <td>
            <?php echo $item['edit_link']; ?>
        </td>
        <td>
            <?php echo $item['dat']; ?>
        </td>
        <td>
            <?php echo $item['amount']; ?>
        </td>
        <td>
            <?php echo $item['score_link']; ?>
        </td>
        <td>
            <?php echo $item['score_date']; ?>
        </td>
        <td>
            <?php echo $item['contract_link']; ?>
        </td>
        <td>
            <?php echo $item['contract_date']; ?>
        </td>
        <td>
            <?php echo $item['company_link']; ?>
        </td>
        <td>
            <?php echo $item['payer_link']; ?>
        </td>
        <td>
            <?php echo $item['id']; ?>
        </td>
    </tr>
<?php endforeach; ?>