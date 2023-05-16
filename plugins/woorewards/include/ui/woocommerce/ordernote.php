<?php
namespace LWS\WOOREWARDS\Ui\Woocommerce;

// don't call the file directly
if( !defined( 'ABSPATH' ) ) exit();

/** Add our own metabox to show WooRewards relevant order notes. */
class OrderNote
{
	public static function install()
	{
		\add_action('add_meta_boxes', function(){
			$me = new \LWS\WOOREWARDS\Ui\Woocommerce\OrderNote();
			\add_meta_box(
				'woorewards-order-notes',
				__('Loyalty system notes', 'woorewards-lite'),
				array($me, 'eContent'),
				'shop_order',
				'side', 'default'
			);
		}, 1000); // let wc at above
	}

	/**	echo box content
	 * @param $post (WP_Post|WC_Order) $post Post or order object. */
	public function eContent($post)
	{
		$orderId = (\is_a($post, '\WC_Order') ? $post->get_id() : $post->ID);
		$notes = \LWS\WOOREWARDS\Core\OrderNote::get($orderId);
		if ($notes) {
			$content = '';
			foreach ($notes as $note) {
				$css = \implode(' ', \apply_filters('lws_woorewards_metabox_order_note_class', array('note'), $note));
				$row = <<<EOT
<li rel="%1\$s" class="{$css}">
	<div class="note_content">%4\$s</div>
	<p class="meta">
		<abbr class="exact-date" title="%2\$s">%3\$s</abbr>
	</p>
</li>
EOT;
				$date = \wc_string_to_datetime($note->comment_date);
				$content .= \apply_filters('lws_woorewards_metabox_order_note_item', sprintf(
					$row,
					\absint($note->comment_ID),
					\esc_attr($date->date('Y-m-d H:i:s')),
					\esc_html(sprintf(
						__('%1$s at %2$s', 'woorewards-lite'),
						$date->date_i18n(\wc_date_format()),
						$date->date_i18n(\wc_time_format())
					)),
					\wpautop(\wptexturize(\wp_kses_post($note->comment_content)))
				), $note);
			}
			echo "<ul class='woorewards-notes order_notes'>{$content}</ul>";
		} else {
			echo sprintf(
				'<ul class="woorewards-notes order_notes"><li class="no-items">%s</li></ul>',
				__( 'There are no notes yet.', 'woorewards-lite')
			);
		}
	}
}