<?php echo $this->blocks_separator(); // phpcs:ignore  ?>
<div class="jet-countdown-timer__item item-hours">
	<div class="jet-countdown-timer__item-value" data-value="hours"><?php echo $this->date_placeholder(); // phpcs:ignore ?></div>
	<?php $this->_html( 'label_hours', '<div class="jet-countdown-timer__item-label">%s</div>' ); // phpcs:ignore  ?>
</div>
