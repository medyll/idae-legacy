<?php
	/**
	 * Created by PhpStorm.
	 * User: Mydde
	 * Date: 25/08/2015
	 * Time: 13:51
	 */

	namespace appServer;

	use Ratchet\MessageComponentInterface;
	use Ratchet\ConnectionInterface;

	class Chat implements MessageComponentInterface {
		public function onOpen(ConnectionInterface $conn) {
		}

		public function onMessage(ConnectionInterface $from, $msg) {
		}

		public function onClose(ConnectionInterface $conn) {
		}

		public function onError(ConnectionInterface $conn, \Exception $e) {
		}
	}