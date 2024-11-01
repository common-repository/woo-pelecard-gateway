<?php

namespace Pelecardwc;

use Pelecardwc\Traits\Singleton;

/**
 * Class Session
 */
class Session extends \WC_Session_Handler {

	use Singleton;

	public function set_customer_id( int $customer_id ) {
		$this->_customer_id = $customer_id;

		return $this;
	}

	public function set( $key, $value ) {
		parent::set( $key, $value );

		return $this;
	}

	public function init_current_session_data() {
		$this->_data = $this->get_session_data();

		return $this;
	}

	public function has_session() {
		return parent::has_session() || 0 < $this->_customer_id;
	}

	public function set_notices( $notices ) {
		$this->set( 'wc_notices', $notices );

		return $this;
	}
}
