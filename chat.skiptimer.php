<?php
 /*********************************************************\
 *                         Skiptmer                        *
 ***********************************************************
 *                        Features                         *
 * - Adds a chatcommand with which you can start a timer   *
 *   to skip to the next map.                              *
 * - /skiptimer <Amount> <min/sec/h>                       *
 * - Only Admins and Masteradmin can acces this command    *
 *   by default. Change the permissions in line 89         *
 ***********************************************************
 *          Created by Malun | Idea by nom2nom             *
 ***********************************************************
 *                    Dependencies: none                   *
 ***********************************************************
 *                         License                         *
 * LICENSE: This program is free software: you can         *
 * redistribute it and/or modify it under the terms of the *
 * GNU General Public License as published by the Free     *
 * Software Foundation, either version 3 of the License,   *
 * or (at your option) any later version.                  *
 *                                                         *
 * This program is distributed in the hope that it will be *
 * useful, but WITHOUT ANY WARRANTY; without even the      *
 * implied warranty of MERCHANTABILITY or FITNESS FOR A    *
 * PARTICULAR PURPOSE.  See the GNU General Public License *
 * for more details.                                       *
 *                                                         *
 * You should have received a copy of the GNU General      *
 * Public License along with this program.  If not,        *
 * see <http://www.gnu.org/licenses/>.                     *
 ***********************************************************
 *                       Installation                      *
 * - Put this plugin in /Controllers/XASECO/plugins        *
 * - activate the plugin in                                *
 *   /TMF04445/Controllers/XASECO/plugins.xml              *
 * - If you're not interested in the Warningmessages,      *
 *   simply comment them out                               *
 \*********************************************************/

global $st_skiptime, $st_SkiptimerActivated, $st_SkipTimeAmount, $st_perm;

//config
//[3]: Only Masteradmin, [2]: Masteradmin and Admin, [1]: Masteradmin, Admin and Operator, [0]: No permissions required
$st_perm = 2;

Aseco::registerEvent("onRestartChallenge", "skiptimer_resettimer");
Aseco::registerEvent("onNewChallenge", "skiptimer_resettimer");
Aseco::registerEvent("onEverySecond", "skiptimer_onEverySecond");
 
Aseco::addChatCommand("skiptimer","Creates the skiptimer environment");

function skiptimer_resettimer($aseco) {
	global $st_skiptime, $st_SkiptimerActivated, $st_SkipTimeAmount;
	
	unset($GLOBALS['st_skiptime']);
	unset($GLOBALS['st_SkipTimeAmount']);
	$st_SkiptimerActivated = false;
 }
 
function skiptimer_onEverySecond($aseco,$st_SkiptimerActivated = false) {
	global $st_skiptime, $st_SkiptimerActivated;
	
	if ($st_SkiptimerActivated == true && $st_skiptime <= time()) {
		//Variable Skip Warning messages
		if ($st_skiptime - $st_skiptimeamount / 2 == time()) { //50% timer Warning
			$aseco->client->query('ChatSendServerMessage', '$z$s>> The map will be skipped in $b00'. ($st_skiptimeamount / 2) . ' $z$sminute.');
		}
		elseif ($st_skiptime - $st_skiptimeamount / 10 == time()) { //10% timer Warning
			$aseco->client->query('ChatSendServerMessage', '$z$s>> The map will be skipped in $b00'. ($st_skiptimeamount / 10) . ' $z$sminute.');
		}
		//Fixed Skip Warning messages
		elseif ($st_skiptime - 60 == time() && $st_skiptimeamount != 60) { //One minute warning
			$aseco->client->query('ChatSendServerMessage', '$z$s>> The map will be skipped in $b00one $z$sminute.');
		}
		elseif ($st_skiptime - 3 == time() && $st_skiptimeamount != 3) { //3 seconds warning
			$aseco->client->query('ChatSendServerMessage', '$z$s>> The map will be skipped in $b00three $z$sseconds.');
		}
		elseif ($st_skiptime - 2 == time() && $st_skiptimeamount != 2) { //2 seconds warning
			$aseco->client->query('ChatSendServerMessage', '$z$s>> The map will be skipped in $b00two $z$ssecond.');
		}
		elseif ($st_skiptime - 1 == time() && $st_skiptimeamount != 1) { //1 seconds warning
			$aseco->client->query('ChatSendServerMessage', '$z$s>> The map will be skipped in $b00one $z$ssecond.');
		}
		//Actual skip
	} else ($st_skiptime <= time()) { //Mapskip
			$aseco->client->query('NextChallenge');
			$st_SkiptimerActivated = false;
			unset($SkipTime);	
	}
	
}
 
 function chat_skiptimer($aseco, $command) {
	global $st_skiptime, $st_SkiptimerActivated, $st_SkipTimeAmount, $st_perm;
	
	$player = $command['author'];
	$login = $player->login;
	
	// split params into arrays & insure optional parameters exist. Cloned from chat.admin.php by Xymph
	$arglist = explode(' ', $command['params'], 2);
	if (!isset($arglist[1])) $arglist[1] = '';
	$command['params'] = explode(' ', preg_replace('/ +/', ' ', $command['params']));
	if (!isset($command['params'][1])) $command['params'][1] = '';
	
	//permission regulation
	if ($st_perm == 3) { //only MasterAdmin
		if ($aseco->isMasterAdmin($player) == false) {
			$aseco->client->query('ChatSendServerMessageToLogin', '$z$s>$i$b00 You don\'t have the required permissions to do that.', $login);
			return;
		}
	} elseif ($st_perm == 2) { //MasterAdmin and Admin
		if ($aseco->isMasterAdmin($player) == false && $aseco->isAdmin($player) ==false) {
			$aseco->client->query('ChatSendServerMessageToLogin', '$z$s>$i$b00 You don\'t have the required permissions to do that.', $login);
			return;
		}
	} elseif ($st_perm == 1) { //MasterAdmin, Admin and Operator
		if ($aseco->isMasterAdmin($player) == false && $aseco->isAdmin($player) == false && $aseco->isOperator($player) == false) {
			$aseco->client->query('ChatSendServerMessageToLogin', '$z$s>$i$b00 You don\'t have the required permissions to do that.', $login);
			return;
		}
	}
	
	if (is_numeric($command['params'][0]) && $st_SkiptimerActivated != true) {
		if ($command['params'][1] == 'min') {
			$st_skiptime = time() + ($command['params'][0] * 60);
			$st_SkipTimeAmount = $command['params'][0] * 60;
			$st_SkiptimerActivated = true;
			if ($command['params'][0] == '1') {
				$aseco->client->query('ChatSendServerMessage', '$z$s>> A Skiptimer got activated. Map will be skipped in $b00one $z$sminute.');
			} else {
				$aseco->client->query('ChatSendServerMessage', '$z$s>> A Skiptimer got activated. Map will be skipped in $b00' . $command['params'][0] . ' $z$sminutes.');
			}
		} elseif ($command['params'][1] == 'sec') {
			$st_skiptime = time() + $command['params'][0];
			$st_SkipTimeAmount = $command['params'][0];
			$st_SkiptimerActivated = true;
			if ($command['params'][0] == '1') {
				$aseco->client->query('ChatSendServerMessage', '$z$s>> A Skiptimer got activated. Map will be skipped in $b00one $z$ssecond.');
			} else {
				$aseco->client->query('ChatSendServerMessage', '$z$s>> A Skiptimer got activated. Map will be skipped in $b00' . $command['params'][0] . ' $z$sseconds.');
			}
		} elseif ($command['params'][1] == 'h') {
			$st_skiptime = time() + ($command['params'][0] * 60 * 60);
			$st_SkipTimeAmount = $command['params'][0] * 60 * 60;
			$st_SkiptimerActivated = true;
			if ($command['params'][0] == '1') {
				$aseco->client->query('ChatSendServerMessage', '$z$s>> A Skiptimer got activated. Map will be skipped in $b00one $z$shour.');
			} else {
				$aseco->client->query('ChatSendServerMessage', '$z$s>> A Skiptimer got activated. Map will be skipped in $b00' . $command['params'][0] . ' $z$shours.');
			}
		}
		elseif ($command['params'][1] == '') {
			$aseco->client->query('ChatSendServerMessageToLogin', '$z$s> $i$b00You need to enter an additional paramater. Use $z$s</skiptimer help>$i$b00 for further information.', $login);
		} else {
			$aseco->client->query('ChatSendServerMessageToLogin', '$z$s> ' . $command['params'][1] . '$i$b00 is not a valid unit. Use $z$s<min>$i$b00, $z$s<sec>$i$b00 or $z$s<h>$i$b00 instead.', $login);
		}			
	} elseif ($command['params'][0] == 'help') {
			$aseco->client->query('ChatSendServerMessageToLogin', '$z$s> Use /skiptimer <Amount> <min/sec/h> to start a Skiptimer. Use a dot [.] for decimal numbers.', $login);
	} elseif ($command['params'][0] == 'cancel') {
		if ($st_SkiptimerActivated == true) {
			$st_SkiptimerActivated = false;
			unset($GLOBALS['st_skiptime']);
			$aseco->client->query('ChatSendServerMessage', '$z$s>> The current Skiptimer got deactivated.');
		} elseif ($st_SkiptimerActivated == false) {
			$aseco->client->query('ChatSendServerMessageToLogin', '$z$s> $i$b00Currently there is no Skiptimer active.', $login);
		}
	} elseif ($st_SkiptimerActivated == true) {
		$aseco->client->query('ChatSendServerMessageToLogin', '$z$s> $i$b00There is already a Skiptimer active. Use $z$s</skiptimer cancel>$i$b00 to cancel it.', $login);
	} else {
		$aseco->client->query('ChatSendServerMessageToLogin', '$z$s>$i$b00 Use $z$s/skiptimer help$i$b00 for further information.', $login);
	}
 } 
 ?>