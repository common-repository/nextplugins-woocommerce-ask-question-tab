<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $product;
?>
<script type="text/javascript">
	jQuery(function ($) {
		$('#contact-form').on('submit', function (e) {
			if (!e.isDefaultPrevented()) {
				var url = "<?php echo esc_url( admin_url( 'admin-ajax.php' )) ?>";
				var message_obj = $('#contact-form').find('.messages');
				message_obj.empty();

				$.ajax({
					type: "POST",
					dataType: "json",
					url: url,
					data: $(this).serialize(),
					success: function (data)
					{
						if(data.has_error == false)
						{
							message_obj.html('<div class="woocommerce-message">' + data.messages[0] + '</div>');
                            $('html, body').animate({ scrollTop: $('#contact-form .messages').offset().top}, 1000);
							$('#contact-form')[0].reset();
						}

						if(data.has_error == true)
						{
							var message = '';

							$.each( data.messages, function( key, value ) {
								message += '<div class="woocommerce-error">' + value + '</div>';
							});

							message_obj.html(message);

                            $('html, body').animate({ scrollTop: $('#contact-form .messages').offset().top}, 1000);
						}
					}
				});
				return false;
			}
		})
	});
</script>
<form id="contact-form">
	<input type="hidden" name="action" value="send_product_question">
	<input type="hidden" name="product" value="<?php echo esc_url(get_permalink( $product->id )) ?>">
	<div class="messages"></div>

	<div class="form">
		<label for="form_name"><?php _e('Name', 'nextplugins-woocommerce-ask-question-tab'); ?> *</label>
		<input id="form_name" type="text" name="name" required="required" style="width: 100%;clear: both;margin-bottom: 5px">

		<div style="display: none !important;"> <?php //simple bot protection ?>
			<label for="form_lastname"><?php _e('Lastname', 'nextplugins-woocommerce-ask-question-tab'); ?></label>
			<input id="form_lastname" type="text" name="surname">
		</div>

		<label for="form_phone"><?php _e('Phone', 'nextplugins-woocommerce-ask-question-tab'); ?></label>
		<input id="form_phone" type="text" name="phone" style="width: 100%;clear: both;margin-bottom: 5px">

		<label for="form_email"><?php _e('Email', 'nextplugins-woocommerce-ask-question-tab'); ?></label>
		<input id="form_email" type="email" name="email" style="width: 100%;clear: both;margin-bottom: 5px">

		<label for="form_message"><?php _e('Message', 'nextplugins-woocommerce-ask-question-tab'); ?> *</label>
		<textarea id="form_message" name="message" rows="4" required="required" style="margin-bottom: 10px"></textarea>
		<br>
		<input type="text" name="human" placeholder="* + 8 = 13" required="required" style="width: 100%;clear: both;margin-bottom: 10px">
        <input type="hidden" name="check" value="<?php echo wp_create_nonce( 'nextplugins_check' ) ?>">
		<br>
		<p class="form-submit"><input type="submit" class="submit" value="<?php _e('Send message', 'nextplugins-woocommerce-ask-question-tab'); ?>"></p>
	</div>
</form>