<?php
namespace LWS\WOOREWARDS\PRO\Unlockables;

// don't call the file directly
if( !defined( 'ABSPATH' ) ) exit();

/**
 * Create a WooCommerce Coupon. */
class CustomReward extends \LWS\WOOREWARDS\Abstracts\Unlockable
{

	function getInformation()
	{
		return array_merge(parent::getInformation(), array(
			'icon'  => 'lws-icon-menu-5',
			'short' => __("Set a totally custom reward to send to your customers. Once they earn it, you will receive an email telling them they won it.", 'woorewards-pro'),
			'help'  => __("This reward is totally up to you, it's not linked to any WooCommerce feature.", 'woorewards-pro'),
		));
	}

	function getData($min=false)
	{
		$prefix = $this->getDataKeyPrefix();
		$data = parent::getData();
		$data[$prefix.'todo'] = $this->getTodo();
		return $data;
	}

	function getForm($context='editlist')
	{
		$prefix = $this->getDataKeyPrefix();
		$form = parent::getForm($context);
		$form .= $this->getFieldsetBegin(2, __("Administration", 'woorewards-pro'), 'col50');

		// todo
		$label = _x("Todo", "CustomReward", 'woorewards-pro');
		$placeholder = \esc_attr(\apply_filters('the_wre_unlockable_description', $this->getDescription('edit'), $this->getId()));
		$value = \htmlspecialchars($this->getTodo(), ENT_QUOTES);
		$form .= "<div class='lws-$context-opt-title label'>$label</div>";
		$form .= "<div class='lws-$context-opt-input value'>";
		$form .= "<textarea id='{$prefix}todo' name='{$prefix}todo' placeholder='$placeholder'>$value</textarea>";
		$form .= "</div>";

		$form .= $this->getFieldsetEnd(2);
		return $form;
	}

	function submit($form=array(), $source='editlist')
	{
		$prefix = $this->getDataKeyPrefix();
		$values = \apply_filters('lws_adminpanel_arg_parse', array(
			'post'     => ($source == 'post'),
			'values'   => $form,
			'format'   => array(
				$prefix.'todo' => 't'
			),
			'defaults' => array(
				$prefix.'todo' => ''
			),
			'labels'   => array(
				$prefix.'todo' => _x("Todo", "CustomReward", 'woorewards-pro')
			)
		));
		if( !(isset($values['valid']) && $values['valid']) )
			return isset($values['error']) ? $values['error'] : false;

		$valid = parent::submit($form, $source);
		if( $valid === true )
		{
			$this->setTodo($values['values'][$prefix.'todo']);
		}
		return $valid;
	}

	public function getTodo()
	{
		return isset($this->todo) ? $this->todo : '';
	}

	public function setTodo($todo='')
	{
		$this->todo = $todo;
		return $this;
	}

	public function setTestValues()
	{
		$this->setTodo(__("This is a test. Just ignore it.", 'woorewards-pro'));
		return $this;
	}

	protected function _fromPost(\WP_Post $post)
	{
		$this->setTodo(\get_post_meta($post->ID, 'woorewards_custom_todo', true));
		return $this;
	}

	protected function _save($id)
	{
		\update_post_meta($id, 'woorewards_custom_todo', $this->getTodo());
		return $this;
	}

	/** sends a mail to the administrator with the user information
	 * and the Text specified in the loyalty grid */
	public function createReward(\WP_User $user, $demo=false)
	{
		if( !$demo )
		{
			$admin = \get_option('admin_email');
			if( \is_email($admin) )
			{
				$body = '<p>' . __("A user unlocked a custom reward.", 'woorewards-pro');
				$body .= '<br/><h2>' . $this->getTitle() . '</h2>';
				$body .= '<h3>' . $this->getCustomDescription() . '</h3></p>';

				$body .= '<p>' . __("It is now up to you to:", 'woorewards-pro');
				$body .= '<blockquote>' . $this->getTodo() . '</blockquote></p>';

				$body .= '<p>' . __("The recipient is:", 'woorewards-pro') . '<ul>';
				$body .= sprintf("<li>%s : <b>%s</b></li>", __("E-mail", 'woorewards-pro'), $user->user_email);
				if( !empty($user->user_login) )
					$body .= sprintf("<li>%s : <b>%s</b></li>", __("Login", 'woorewards-pro'), $user->user_login);
				if( !empty($user->display_name) )
					$body .= sprintf("<li>%s : <b>%s</b></li>", __("Name", 'woorewards-pro'), $user->display_name);
				if( !empty($addr = $this->getShippingAddr($user, 'shipping')) )
					$body .= sprintf("<li>%s : <div>%s</div></li>", __("Shipping address", 'woorewards-pro'), implode('<br/>', $addr));
				if( !empty($addr = $this->getShippingAddr($user, 'billing')) )
					$body .= sprintf("<li>%s : <div>%s</div></li>", __("Billing address", 'woorewards-pro'), implode('<br/>', $addr));
				$body .= '</ul></p>';

				\wp_mail(
					$admin,
					__("A customer unlocked the following reward: ", 'woorewards-pro') . $this->getTitle(),
					$body,
					array('Content-Type: text/html; charset=UTF-8')
				);
			}
			else
				error_log("Cannot get a valid administrator email (see options 'admin_email')");

			$data = array(
				'post_name'    => \sanitize_key($this->getDisplayType()),
				'post_title'   => $this->getTitle(),
				'post_status'  => 'draft',
				'post_type'    => 'lws_custom_reward',
				'post_content' => $this->getTodo(),
				'post_excerpt' => $this->getCustomDescription(),
				'meta_input'   => array(
					'user_email'       => $user->user_email,
					'user_id'          => $user->ID,
					'reward_origin'    => $this->getType(),
					'reward_origin_id' => $this->getId(),
					'thumbnail'        => $this->getThumbnail()
				)
			);

			if( \is_wp_error($postId = \wp_insert_post($data, true)) )
				error_log("Error occured during custom reward saving: " . $postId->get_error_message());
		}

		return array(
			'todo' => $this->getTodo()
		);
	}

	/** @param $usage must be 'billing' or 'shipping' */
	protected function getShippingAddr($user, $usage='shipping')
	{
		$fname     = \get_user_meta( $user->ID, 'first_name', true );
		$lname     = \get_user_meta( $user->ID, 'last_name', true );
		$address_1 = \get_user_meta( $user->ID, $usage . '_address_1', true );
		$city      = \get_user_meta( $user->ID, $usage . '_city', true );

		if( !(empty($address_1) || empty($city)) )
		{
			$postcode = \get_user_meta( $user->ID, $usage . '_postcode', true );
			if( !empty($postcode) )
				$city = $postcode . " " . $city;

			$country = \get_user_meta( $user->ID, $usage . '_country', true );
			$state = \get_user_meta( $user->ID, $usage . '_state', true );
			static $countries = array();
			static $states = array();
			if( empty($countries) && \LWS_WooRewards::isWC() )
			{
				try{
					@include_once WP_PLUGIN_DIR . '/woocommerce/includes/class-wc-countries.php';
					$countries = \WC()->countries->countries;
					$states = \WC()->countries->states;
					if( isset($countries[$country]) )
					{
						if( isset($states[$country]) )
						{
							$lstates = $states[$country];
							if( isset($lstates[$state]) )
								$state = $lstates[$state];
						}
						$country = $countries[$country];
					}
				}catch (Exception $e){
					error_log($e->getMessage());
				}
			}

			return array(
				$fname . ' ' . $lname,
				$address_1,
				\get_user_meta( $user->ID, $usage . '_address_2', true ),
				$city,
				$country,
				$state
			);
		}
		return array();
	}

	public function getDisplayType()
	{
		return _x("Custom reward", "getDisplayType", 'woorewards-pro');
	}

	/** For point movement historic purpose. Can be override to return a reason.
	 *	Last generated coupon code is consumed by this function. */
	public function getReason($context='backend')
	{
		return $this->getCustomDescription();
	}

	/**	Event categories, used to filter out events from pool options.
	 *	@return array with category_id => category_label. */
	public function getCategories()
	{
		return array_merge(parent::getCategories(), array(
			'sponsorship' => _x("Referee", "unlockable category", 'woorewards-pro'),
			'miscellaneous' => __("Miscellaneous", 'woorewards-pro')
		));
	}
}

?>