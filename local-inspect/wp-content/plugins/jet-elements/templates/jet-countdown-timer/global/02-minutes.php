<?php echo $this->blocks_separator(); // phpcs:ignore ?>
<div class="jet-countdown-timer__item item-minutes">
	<div class="jet-countdown-timer__item-value" data-value="minutes"><?php echo $this->date_placeholder(); // phpcs:ignore ?></div>
	<?php $this->_html( 'label_min', '<div class="jet-countdown-timer__item-label">%s</div>' ); // phpcs:ignore ?>
</div>
