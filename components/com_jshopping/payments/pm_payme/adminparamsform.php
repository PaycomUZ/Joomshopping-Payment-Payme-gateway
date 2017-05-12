<?php
//защита от прямого доступа
defined('_JEXEC') or die();
?>
<div class="col100">
	<fieldset class="adminform">
		<table class="admintable" width="100%">
			<tr>
				<td class="key" width="300">
					<?php echo _JSHOP_CFG_PAYME_MERCHANT_ID; ?></td>
				<td>
					<input type="text" name="pm_params[payme_merchant_id]" class="inputbox" value="<?php echo $params['payme_merchant_id']; ?>" />
				</td>
			</tr>
			<tr>
				<td class="key">
					<?php echo _JSHOP_CFG_PAYME_SECRET_KEY; ?>
				</td>
				<td>
					<input type="text" name="pm_params[payme_secret_key]" class="inputbox" value="<?php echo $params['payme_secret_key'];?>" />
				</td>
			</tr>
			<tr>
				<td class="key" width="300">
					<?php echo _JSHOP_PAYME_METHOD_ID; ?></td>
				<td>
					<input type="text" name="pm_params[payme_method_id]" class="inputbox" value="<?php echo $params['payme_method_id']; ?>" />
				</td>
			</tr>
			<tr>
				<td class="key" width="300">
					<?php echo _JSHOP_PAYME_DB_HOST; ?></td>
				<td>
					<input type="text" name="pm_params[payme_db_host]" class="inputbox" value="<?php echo $params['payme_db_host']; ?>" />
				</td>
			</tr>
			<tr>
				<td class="key" width="300">
					<?php echo _JSHOP_PAYME_DB_NAME; ?></td>
				<td>
					<input type="text" name="pm_params[payme_db_name]" class="inputbox" value="<?php echo $params['payme_db_name']; ?>" />
				</td>
			</tr>
          <tr>
				<td class="key" width="300">
					<?php echo _JSHOP_PAYME_DB_SUFFICS; ?></td>
				<td>
					<input type="text" name="pm_params[payme_db_suf]" class="inputbox" value="<?php echo $params['payme_db_suf']; ?>" />
				</td>
			</tr>
			<tr>
				<td class="key" width="300">
					<?php echo _JSHOP_PAYME_DB_USER; ?></td>
				<td>
					<input type="text" name="pm_params[payme_db_user]" class="inputbox" value="<?php echo $params['payme_db_user']; ?>" />
				</td>
			</tr>
			<tr>
				<td class="key" width="300">
					<?php echo _JSHOP_PAYME_DB_PASS; ?></td>
				<td>
					<input type="text" name="pm_params[payme_db_pass]" class="inputbox" value="<?php echo $params['payme_db_pass']; ?>" />
				</td>
			</tr>
      <tr>
				<td class="key">
					<?php echo _JSHOP_PAYME_PAID; ?>
				</td>
				<td>
				<?php              
					echo JHTML::_('select.genericlist', $orders->getAllOrderStatus(), 'pm_params[transaction_end_status]', 'class="inputbox" size="1"', 'status_id', 'name', $params['transaction_end_status']);
				?>
				</td>
			</tr>
			<tr>
				<td class="key">
					<?php echo _JSHOP_PAYME_PENDING; ?>
				</td>
				<td>
				<?php 
					echo JHTML::_('select.genericlist', $orders->getAllOrderStatus(), 'pm_params[transaction_pending_status]', 'class="inputbox" size="1"', 'status_id', 'name', $params['transaction_pending_status']);
				?>
				</td>
			</tr>
			<tr>
				<td class="key">
					<?php echo _JSHOP_PAYME_CANCELED; ?>
				</td>
				<td>
				<?php 
					echo JHTML::_('select.genericlist', $orders->getAllOrderStatus(), 'pm_params[transaction_cancel_status]', 'class="inputbox" size="1"', 'status_id', 'name', $params['transaction_cancel_status']);
				?>
				</td>
			</tr>
			<tr>
				<td class="key">
					<?php echo _JSHOP_PAYME_REFUNDED; ?>
				</td>
				<td>
				<?php 
					echo JHTML::_('select.genericlist', $orders->getAllOrderStatus(), 'pm_params[transaction_refunded_status]', 'class="inputbox" size="1"', 'status_id', 'name', $params['transaction_refunded_status']);
				?>
				</td>
			</tr>
			<tr>
				<td class="key">
					<?php echo _JSHOP_PAYME_CONFIRMED; ?>
				</td>
				<td>
				<?php 
					echo JHTML::_('select.genericlist', $orders->getAllOrderStatus(), 'pm_params[transaction_confirm_status]', 'class="inputbox" size="1"', 'status_id', 'name', $params['transaction_confirm_status']);
				?>
				</td>
			</tr>
          <tr>
				<td class="">
					<?php echo "Payment notification:"; ?>
				</td>
				<td>
				<?php 
					echo "http://your_site/index.php?option=com_jshopping&controller=checkout&task=step7&act=notify&js_paymentclass=pm_payme";
				?>
				</td>
			</tr>
		</table>
	</fieldset>
</div>
<div class="clr"></div>