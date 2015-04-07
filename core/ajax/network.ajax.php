<?php

/* This file is part of Jeedom.
 *
 * Jeedom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jeedom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
 */

try {
	require_once dirname(__FILE__) . '/../../core/php/core.inc.php';
	include_file('core', 'authentification', 'php');

	if (init('action') == 'restartNgrok') {
		config::save('market::allowDNS', 1);
		if (network::ngrok_run()) {
			network::ngrok_stop();
		}
		network::ngrok_start();
		if (config::byKey('market::redirectSSH') == 1) {
			if (network::ngrok_run('tcp', 22, 'ssh')) {
				network::ngrok_stop('tcp', 22, 'ssh');
			}
			network::ngrok_start('tcp', 22, 'ssh');
		} else {
			if (network::ngrok_run('tcp', 22, 'ssh')) {
				network::ngrok_stop('tcp', 22, 'ssh');
			}
		}
		ajax::success();
	}

	if (init('action') == 'stopNgrok') {
		config::save('market::allowDNS', 0);
		network::ngrok_stop();
		if (config::byKey('market::redirectSSH') == 1) {
			network::ngrok_stop('tcp', 22, 'ssh');
		}
		ajax::success();
	}

	throw new Exception(__('Aucune methode correspondante à : ', __FILE__) . init('action'));
	/*     * *********Catch exeption*************** */
} catch (Exception $e) {
	ajax::error(displayExeption($e), $e->getCode());
}
?>
