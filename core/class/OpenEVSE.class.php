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

/* * ***************************Includes********************************* */
require_once __DIR__  . '/../../../../core/php/core.inc.php';

class OpenEVSE extends eqLogic {
    /*     * *************************Attributs****************************** */

    /*     * ***********************Methode static*************************** */

   	//Fonction exécutée automatiquement toutes les minutes par Jeedom
    public static function cron() {
		foreach (self::byType('OpenEVSE') as $OpenEVSE) {//parcours tous les équipements du plugin OpenEVSE
			if ($OpenEVSE->getIsEnable() == 1) {//vérifie que l'équipement est actif
				$cmd = $OpenEVSE->getCmd(null, 'refresh');//retourne la commande "refresh si elle existe
				if (!is_object($cmd)) {//Si la commande n'existe pas
					continue; //continue la boucle
				}
				$cmd->execCmd(); // la commande existe on la lance
			}
		}
    }
	
	public function SetSliderSetPoint($valueSlider) {
		try {
          
          	if ($valueSlider==0) {return;}
          
          	$Mode = $this->getConfiguration("Mode");
			$OpenEVSE_IP = $this->getConfiguration("IP");
			$ch = curl_init();
            
          	//API Mode 1 is the new WIFI API
          	//API Mode 0 is the obsolete RAPI
          
			if ($Mode == 1) {
            	curl_setopt_array($ch, [
  					CURLOPT_URL => 'http://'.$OpenEVSE_IP.'/config',
  					CURLOPT_RETURNTRANSFER => true,
  					CURLOPT_ENCODING => "",
  					CURLOPT_MAXREDIRS => 10,
  					CURLOPT_TIMEOUT => 10,
  					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  					CURLOPT_CUSTOMREQUEST => 'POST',
                  	CURLOPT_POSTFIELDS => '{max_current_soft:'.$valueSlider.'}',
  					CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
                ]);
				$response = curl_exec($ch);
				$err = curl_error($ch);
				curl_close($ch);
				if ($err) {
                   	log::add('OpenEVSE', 'debug','Fonction SetSliderSetPoint : Erreur CURL (WIFI API) -> ').$err;
                } else {
                   	log::add('OpenEVSE', 'debug','Fonction SetSliderSetPoint : Changement référence intensité à '.$valueSlider.' ampères (WIFI API) -> ' .$response);
                  	return $valueSlider;
				}
            } else {
              curl_setopt_array($ch, [
  					CURLOPT_URL => 'http://'.$OpenEVSE_IP.'/r?rapi=$SC%20'.$valueSlider,
  					CURLOPT_RETURNTRANSFER => true,
  					CURLOPT_ENCODING => "",
  					CURLOPT_MAXREDIRS => 10,
  					CURLOPT_TIMEOUT => 10,
  					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  					CURLOPT_CUSTOMREQUEST => 'GET',
                ]);
				$data = curl_exec($ch);
              	curl_close($ch);
				if (curl_errno($ch)) {
					log::add('OpenEVSE', 'debug','Fonction SetSliderSetPoint : Erreur CURL (RAPI) -> ').curl_error($ch);
				} else {
					log::add('OpenEVSE', 'debug','Fonction SetSliderSetPoint : Changement référence intensité à '.$valueSlider.' ampères (RAPI)');
                  	return $valueSlider;
				}
            }
		} catch (Exception $e) {
			log::add('OpenEVSE', 'error', __('Erreur lors de l\'éxecution de SetSliderSetPoint ' . ' ' . $e->getMessage()));
       	}
	}
	
	public function SetStartStop($StartStop) {
		try {
          	$Mode = $this->getConfiguration("Mode");
			$OpenEVSE_IP = $this->getConfiguration("IP");
			$ch = curl_init();
          
          	if ($Mode == 1) {
              	$setopt = '{state:disabled}';
              	switch ($StartStop) {
					case ('Start'):
						$setopt = '{state:"active"}';
						break;
					case ('Stop'):
						$setopt = '{state:"disabled"}';
						break;
					case ('Pause'):
						$setopt = '{state:"disabled"}';
						break;                
				}
            	curl_setopt_array($ch, [
  					CURLOPT_URL => 'http://'.$OpenEVSE_IP.'/override',
  					CURLOPT_RETURNTRANSFER => true,
  					CURLOPT_ENCODING => "",
  					CURLOPT_MAXREDIRS => 10,
  					CURLOPT_TIMEOUT => 10,
  					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  					CURLOPT_CUSTOMREQUEST => 'POST',
                  	CURLOPT_POSTFIELDS => $setopt,
  					CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
                ]);
				$response = curl_exec($ch);
				$err = curl_error($ch);
              	curl_close($ch);
             	if (curl_errno($ch)) {
					log::add('OpenEVSE', 'debug','Fonction SetStartStop : Erreur CURL (WIFI API) -> ').$err;
				} else {
					log::add('OpenEVSE', 'debug','Fonction SetStartStop : Changement valeur à '.$StartStop.' (WIFI API) -> ' .$response);
				}
            } else {
              	$setopt = 'http://'.$OpenEVSE_IP.'/r?rapi=$FD';
				switch ($StartStop) {
					case ('Start'):
						$setopt = 'http://'.$OpenEVSE_IP.'/r?rapi=$FE';
						break;
					case ('Stop'):
						$setopt = 'http://'.$OpenEVSE_IP.'/r?rapi=$FD';
						break;
					case ('Pause'):
						$setopt = 'http://'.$OpenEVSE_IP.'/r?rapi=$FS';
						break;                
				}
              	curl_setopt($ch, CURLOPT_URL, $setopt);
				curl_exec($ch);
              	curl_close($ch);
				if (curl_errno($ch)) {
					log::add('OpenEVSE', 'debug','Fonction SetStartStop : Erreur CURL (RAPI) -> ').curl_error($ch);
				} else {
					log::add('OpenEVSE', 'debug','Fonction SetStartStop : Changement valeur à '.$StartStop.' (RAPI)');
				}
            }
          	$this->GetData();
		} catch (Exception $e) {
			log::add('OpenEVSE', 'error', __('Erreur lors de l\'éxecution de SetStop ' . ' ' . $e->getMessage()));
		}
              
	}
	
	public function SetMode($SelMode) {
		try {
			switch ($SelMode) {
				case 'Man':
					$this->checkAndUpdateCmd('EVSE_Mode', 'Manuel');
					break;
				case 'Auto':
					$this->checkAndUpdateCmd('EVSE_Mode', 'Automatique');
					break;
			}
			log::add('OpenEVSE', 'debug','Fonction SetMode : Changement valeur à '.$SelMode);
			return;
		} catch (Exception $e) {
			log::add('OpenEVSE', 'error', __('Erreur lors de l\'éxecution de SetMode ' . ' ' . $e->getMessage()));
		}
	}

	public function get_string_between($string, $start, $end){
		$string = ' ' . $string;
		$ini = strpos($string, $start);
		if ($ini == 0) return '';
		$ini += strlen($start);
		$len = strpos($string, $end, $ini) - $ini;
		return substr($string, $ini, $len);
	}
	
	public function SetVoltageRef($RefVolts) {
		try {
			$RefVolts = round($RefVolts,0);
			if ($RefVolts < 0 || $RefVolts > 500) {
				log::add('OpenEVSE', 'debug','Fonction SetVoltageRef : Référence voltage depuis la commande spécifiée est incorrecte : '.$RefVolts);
			} else {
              	$Mode = $this->getConfiguration("Mode");
				$setpointVolts = $RefVolts * 1000;
				$OpenEVSE_IP = $this->getConfiguration("IP");
				$ch = curl_init();
              
          		//API Mode 1 is the new WIFI API
          		//API Mode 0 is the obsolete RAPI
          
				if ($Mode == 1) {
                	curl_setopt_array($ch, [
  						CURLOPT_URL => 'http://'.$OpenEVSE_IP.'/status',
  						CURLOPT_RETURNTRANSFER => true,
  						CURLOPT_ENCODING => "",
  						CURLOPT_MAXREDIRS => 10,
  						CURLOPT_TIMEOUT => 10,
  						CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  						CURLOPT_CUSTOMREQUEST => 'POST',
  						CURLOPT_POSTFIELDS => '{voltage:'.$RefVolts.'}',
  						CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
                   	]);
					$response = curl_exec($ch);
					$err = curl_error($ch);
					curl_close($ch);
					if ($err) {
                      	log::add('OpenEVSE', 'debug','Fonction SetVoltageRef : Erreur CURL (WIFI API) -> ').$err;
                    } else {
                      	log::add('OpenEVSE', 'debug','Fonction SetVoltageRef : Changement référence voltage à '.$RefVolts.' volts (WIFI API) -> ' .$response);
					}
                } else {
                  	curl_setopt_array($ch, [
  						CURLOPT_URL => 'http://'.$OpenEVSE_IP.'/r?rapi=$SV%20'.$setpointVolts,
  						CURLOPT_RETURNTRANSFER => true,
  						CURLOPT_ENCODING => "",
  						CURLOPT_MAXREDIRS => 10,
  						CURLOPT_TIMEOUT => 10,
  						CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  						CURLOPT_CUSTOMREQUEST => 'POST',
                   	]);
					$data = curl_exec($ch);
                  	curl_close($ch);
					if (curl_errno($ch)) {
						log::add('OpenEVSE', 'debug','Fonction SetVoltageRef : Erreur CURL (RAPI) -> ').curl_error($ch);
					} else {
						log::add('OpenEVSE', 'debug','Fonction SetVoltageRef : Changement référence voltage à '.$RefVolts.' volts (RAPI)');
					}  
                }
				
			}
		} catch (Exception $e) {
			log::add('OpenEVSE', 'error', __('Erreur lors de l\'éxecution de SetVoltageRef ' . ' ' . $e->getMessage()));
		}			
	}
  
  	public function AdjustVoltage($volts,$APIType) {
    	// Adjust OpenEVSE Volts if Volts Reference Command value is different from charger
		$sendVoltsCmd = $this->getConfiguration('sendVoltsCmd', '');
		if (strlen($sendVoltsCmd)>0) {
			if (is_numeric($sendVoltsCmd)) {
				$cmdVolts = round($sendVoltsCmd,0);
				if ($volts != $cmdVolts) {
					$this->SetVoltageRef($cmdVolts);
				}
			} else {
               	if (stripos($sendVoltsCmd, 'variable(') !== false && stripos($sendVoltsCmd, ')') !== false) {
                   	$sendVoltsCmd = $this->get_string_between($sendVoltsCmd,'(',')');
                   	$cmd = dataStore::byTypeLinkIdKey('scenario', -1, $sendVoltsCmd);
					if (!is_object($cmd)) {
                       	log::add('OpenEVSE', 'debug', "Fonction GetData : Variable '{$sendVoltsCmd}' non trouvée ($APIType) -> vérifiez la configuration pour  {$this->getHumanName()}.");
                    } else {
						$cmdVolts = $cmd->getValue();
                       	if (is_numeric($cmdVolts)) {
							$cmdVolts = round($cmdVolts,0);
							if ($volts != $cmdVolts) {
                               	log::add('OpenEVSE', 'debug',"Fonction GetData : La variable '{$sendVoltsCmd}' retourne une nouvelle valeur numérique ($APIType) -> ".$cmdVolts);
								$this->SetVoltageRef($cmdVolts);
							}
						} else {
							log::add('OpenEVSE', 'debug',"Fonction GetData : La variable '{$sendVoltsCmd}' ne retourne pas une valeur numérique ($APIType) -> ".$cmdVolts);
						}
					}
               	} else {
                   	$cmd = cmd::byId(str_replace('#', '', $sendVoltsCmd));
               		if (!is_object($cmd)) {
						log::add('OpenEVSE', 'debug', "Fonction GetData : Commande '{$sendVoltsCmd}' non trouvée ($APIType) -> vérifiez la configuration pour  {$this->getHumanName()}.");
					}else{
						$cmdVolts = $cmd->execCmd();
						if (is_numeric($cmdVolts)) {
							$cmdVolts = round($cmdVolts,0);
							if ($volts != $cmdVolts) {
                               	log::add('OpenEVSE', 'debug',"Fonction GetData : La commande '{$sendVoltsCmd}' retourne une nouvelle valeur numérique ($APIType) -> ".$cmdVolts);
								$this->SetVoltageRef($cmdVolts);
							}
						} else {
							log::add('OpenEVSE', 'debug',"Fonction GetData : La commande '{$sendVoltsCmd}' ne retourne pas une valeur numérique ($APIType) -> ".$cmdVolts);
						}
					}
            	}
        	}	
		}  
    }
	
	public function GetData() {
		
		try {

          	$Mode = $this->getConfiguration("Mode");
			$OpenEVSE_IP = $this->getConfiguration("IP");
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_TIMEOUT, 10);
          
        	//API Mode 1 is the new WIFI API
          	//API Mode 0 is the obsolete RAPI
          
			if ($Mode == 1) {              
	          	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
				curl_setopt($ch, CURLOPT_URL, 'http://'.$OpenEVSE_IP.'/status');
              	$data = curl_exec($ch);
              	if (curl_errno($ch)) {
					log::add('OpenEVSE', 'debug','Fonction GetData : State - Erreur CURL (WIFI API) -> '.curl_error($ch));
					return;
				}
              	$json = json_decode($data, true);
              
              	// Get OpenEVSE State
  				$state = $json['state'];
             	switch (true) {
                    case ($state == 0):
                   		$this->checkAndUpdateCmd('EVSE_State', 'Unknown');
						break;
					case ($state == 1 || $state == 2):
						$this->checkAndUpdateCmd('EVSE_State', 'ON');
                    	break;
                   	case ($state == 3):
						$this->checkAndUpdateCmd('EVSE_State', 'En Charge');
						break;
                  	case ($state == 4 || $state == 5 || $state == 6 || $state == 7 || $state == 8 || $state == 9 || $state == 10 | $state == 11):
						$this->checkAndUpdateCmd('EVSE_State', 'Erreur');
						break;
                  	case ($state == 254):
						$this->checkAndUpdateCmd('EVSE_State', 'En Pause');
						break;
					case ($state == 255 || $state == 0): // || $arr[0] == 'ff'):
						$this->checkAndUpdateCmd('EVSE_State', 'OFF');
						break;
				}
              
              	// Get OpenEVSE Amperes Set Point
				$setPointEVSE = $json['pilot'];
				$cmd = $this->getCmd(null, 'EVSE_AmpSetPointReadBack');
				$setPointCMD = $cmd->execCmd();
			
				if ($setPointEVSE != $setPointCMD) {
					// Set AmpSetPointReadBack value
					$this->checkAndUpdateCmd('EVSE_AmpSetPointReadBack', $setPointEVSE);
					//Refresh position of the slider
					$cmdAmpSetPointSlider = $this->getCmd(null, 'EVSE_AmpSetPointSlider');
					$options = array('slider'=>round($arr[2],0));
					$cmdAmpSetPointSlider->execCmd($options, $cache=0);
					log::add('OpenEVSE', 'debug','Fonction GetData : Amperes Set Point (WIFI API) -> Rafraîchissement valeur set point intensité à '.$setPointEVSE. ' ampères');
				} else {
					log::add('OpenEVSE', 'debug','Fonction GetData : Amperes Set Point (WIFI API) -> Check valeur set point EVSE vs Plugin OK');
				}
              
              	// Get OpenEVSE Temperature
				$temp = $json['temp'];
				$this->checkAndUpdateCmd('EVSE_Temp', round($temp/10,0));
              
              	// Get OpenEVSE Actual Volts & Amperes
				$amperes = round($json['amp']/1000,1);
				$volts = round($json['voltage'],0);
				$this->checkAndUpdateCmd('EVSE_Amperes', $amperes);
				$this->checkAndUpdateCmd('EVSE_Volts', $volts);
              
				// Adjust OpenEVSE Volts if Volts Reference Command value is different from charger
              	$this->AdjustVoltage($volts,'WIFI API');
				          
				//Get OpenEVSE Plug State
				$connectstate = $json['vehicle'];
				if ($connectstate == 0) {
					$this->checkAndUpdateCmd('EVSE_Plug', 'Déconnectée');
				} elseif ($connectstate == 1) {
					$this->checkAndUpdateCmd('EVSE_Plug', 'Connectée');
				}
              
				// Get OpenEVSE Charge Session in Kwh
				$chargesession = $json['wattsec'];
				$this->checkAndUpdateCmd('EVSE_ChargeSession', round($chargesession/3600000,2));
              
            } else {
				// Get OpenEVSE State
				curl_setopt($ch, CURLOPT_URL, 'http://'.$OpenEVSE_IP.'/r?rapi=$GS');
				$data = curl_exec($ch);
				if (curl_errno($ch)) {
					log::add('OpenEVSE', 'debug','Fonction GetData : State - Erreur CURL (RAPI) -> '.curl_error($ch));
					return;
				}
				$data = $this->get_string_between($data,'OK ','^');
				$arr = explode(" ", $data);
		
				switch (true) {
					case ($arr[0] == '03'):
						$this->checkAndUpdateCmd('EVSE_State', 'En Charge');
						break;
					case ($arr[0] == '02' || $arr[0] == 'fe'): // || $arr[0] == 'ff'):
						$this->checkAndUpdateCmd('EVSE_State', 'En Pause');
						break;
					case ($arr[0] == '01'):
						$this->checkAndUpdateCmd('EVSE_State', 'ON');
						break;
					case ($arr[0] != '00' && $arr[0] != '03'):
						$this->checkAndUpdateCmd('EVSE_State', 'OFF');
						break;
				}
				//$this->checkAndUpdateCmd('EVSE_State', '.'.$data.'.');
			
				// Get OpenEVSE Amperes Set Point
				curl_setopt($ch, CURLOPT_URL, 'http://'.$OpenEVSE_IP.'/r?rapi=$GC');
				$data = curl_exec($ch);
				if (curl_errno($ch)) {
					log::add('OpenEVSE', 'debug','Fonction GetData : Amperes Set Point - Erreur CURL (RAPI) -> '.curl_error($ch));
					return;
				}
				$data = $this->get_string_between($data,'OK ','^');
				$arr = explode(" ", $data);
				$setPointEVSE = round($arr[2],0);
				$cmd = $this->getCmd(null, 'EVSE_AmpSetPointReadBack');
				$setPointCMD = $cmd->execCmd();
			
				if ($setPointEVSE != $setPointCMD) {
					// Set AmpSetPointReadBack value
					$this->checkAndUpdateCmd('EVSE_AmpSetPointReadBack', $setPointEVSE);
					//Refresh position of the slider
					$cmdAmpSetPointSlider = $this->getCmd(null, 'EVSE_AmpSetPointSlider');
					$options = array('slider'=>round($arr[2],0));
					$cmdAmpSetPointSlider->execCmd($options, $cache=0);
					log::add('OpenEVSE', 'debug','Fonction GetData : Amperes Set Point (RAPI) -> Rafraîchissement valeur set point intensité à '.$setPointEVSE. ' ampères');
				} else {
					log::add('OpenEVSE', 'debug','Fonction GetData : Amperes Set Point (RAPI) -> Check valeur set point EVSE vs Plugin OK');
				}
									
				// Get OpenEVSE Temperature
				curl_setopt($ch, CURLOPT_URL, 'http://'.$OpenEVSE_IP.'/r?rapi=$GP');
				$data = curl_exec($ch);
				if (curl_errno($ch)) {
					log::add('OpenEVSE', 'debug','Fonction GetData : Temperature - Erreur CURL (RAPI) -> '.curl_error($ch));
					return;
				}
				$data = $this->get_string_between($data,'OK ','^');
				$arr = explode(" ", $data);
				$this->checkAndUpdateCmd('EVSE_Temp', round($arr[1]/10,0));
			
				// Get OpenEVSE Actual Volts & Amperes
				curl_setopt($ch, CURLOPT_URL, 'http://'.$OpenEVSE_IP.'/r?rapi=$GG');
				$data = curl_exec($ch);
				if (curl_errno($ch)) {
					log::add('OpenEVSE', 'debug','Fonction GetData : Volts-Amperes - Erreur CURL (RAPI) -> '.curl_error($ch));
					return;
				}
				$data = $this->get_string_between($data,'OK ','^');
				$arr = explode(" ", $data);
				$amperes = round($arr[0]/1000,1);
				$volts = round($arr[1]/1000,0);
				$this->checkAndUpdateCmd('EVSE_Amperes', $amperes);
				$this->checkAndUpdateCmd('EVSE_Volts', $volts);
			
				// Adjust OpenEVSE Volts if Volts Reference Command value is different from charger
              	$this->AdjustVoltage($volts,'RAPI');
			
				//Get OpenEVSE Plug State
				curl_setopt($ch, CURLOPT_URL, 'http://'.$OpenEVSE_IP.'/r?rapi=$G0');
				$data = curl_exec($ch);
				if (curl_errno($ch)) {
					log::add('OpenEVSE', 'debug','Fonction GetData : Plug State - Erreur CURL (RAPI) -> '.curl_error($ch));
					return;
				}
				$data = $this->get_string_between($data,'OK ','^');
				$arr = explode(" ", $data);
				if ($arr[0] == 0) {
					$this->checkAndUpdateCmd('EVSE_Plug', 'Déconnectée');
				} elseif ($arr[0] == 1) {
					$this->checkAndUpdateCmd('EVSE_Plug', 'Connectée');
				} elseif ($arr[0] == 2) {
					$this->checkAndUpdateCmd('EVSE_Plug', '...');
				}

				// Get OpenEVSE Charge Session in Kwh
				curl_setopt($ch, CURLOPT_URL, 'http://'.$OpenEVSE_IP.'/r?rapi=$GU');
				$data = curl_exec($ch);
				if (curl_errno($ch)) {
					log::add('OpenEVSE', 'debug','Fonction GetData : Charge Session - Erreur CURL (RAPI) -> '.curl_error($ch));
					return;
				}
				$data = $this->get_string_between($data,'OK ','^');
				$arr = explode(" ", $data);
				$this->checkAndUpdateCmd('EVSE_ChargeSession', round($arr[0]/3600000,2));
            }

			curl_close($ch);

			log::add('OpenEVSE', 'debug','Fonction GetData : Récupération des données OpenEVSE OK !' );
			return;
		} catch (Exception $e) {
			log::add('OpenEVSE', 'error', __('Erreur lors de l\'éxecution de GetData ' . ' ' . $e->getMessage()));
		}
	}
  

    
    /*     * *********************Méthodes d'instance************************* */

    public function preInsert() {
    	$setMode = $this->setConfiguration("Mode",1); //Les nouveaux objets sont de type WIFI API par defaut.
    }

    public function postInsert() {

    }

    public function preSave() {

    }

    public function postSave() {
		$info = $this->getCmd(null, 'EVSE_Volts');
		if (!is_object($info)) {
			$info = new OpenEVSECmd();
			$info->setName(__('Tension : ', __FILE__));
		}
		$info->setLogicalId('EVSE_Volts');
		$info->setEqLogic_id($this->getId());
		$info->setType('info');
		$info->setSubType('numeric');
		$info->setTemplate('dashboard','line');
		$info->setIsHistorized(1);
		$info->setUnite('V');
		$info->setOrder(1);
		$info->save();
		
		$info = $this->getCmd(null, 'EVSE_Amperes');
		if (!is_object($info)) {
			$info = new OpenEVSECmd();
			$info->setName(__('Intensité : ', __FILE__));
		}
		$info->setLogicalId('EVSE_Amperes');
		$info->setEqLogic_id($this->getId());
		$info->setType('info');
		$info->setSubType('numeric');
		$info->setTemplate('dashboard','line');
		$info->setConfiguration('minValue', 0); //$this->getConfiguration("AMin"));
		$info->setConfiguration('maxValue', 32); //$this->getConfiguration("AMax"));
		$info->setIsHistorized(1);
		$info->setUnite('A');
		$info->setOrder(2);
		$info->save();
		
		$info = $this->getCmd(null, 'EVSE_ChargeSession');
		if (!is_object($info)) {
			$info = new OpenEVSECmd();
			$info->setName(__('Charge Session : ', __FILE__));
		}
		$info->setLogicalId('EVSE_ChargeSession');
		$info->setEqLogic_id($this->getId());
		$info->setType('info');
		$info->setSubType('numeric');
		$info->setTemplate('dashboard','line');
		$info->setIsHistorized(1);
		$info->setUnite('Kwh');
		$info->setOrder(3);
		$info->save();
		
		$info = $this->getCmd(null, 'EVSE_Temp');
		if (!is_object($info)) {
			$info = new OpenEVSECmd();
			$info->setName(__('Température : ', __FILE__));
		}
		$info->setLogicalId('EVSE_Temp');
		$info->setEqLogic_id($this->getId());
		$info->setType('info');
		$info->setSubType('numeric');
		$info->setTemplate('dashboard','line');
		$info->setConfiguration('minValue', 0);
		$info->setConfiguration('maxValue', 80);
		$info->setIsHistorized(1);
		$info->setUnite('°C');
		$info->setOrder(4);
		$info->save();
		
		$info = $this->getCmd(null, 'EVSE_Plug');
		if (!is_object($info)) {
			$info = new OpenEVSECmd();
			$info->setName(__('Prise : ', __FILE__));
		}
		$info->setLogicalId('EVSE_Plug');
		$info->setEqLogic_id($this->getId());
		$info->setType('info');
		$info->setSubType('string');
		$info->setTemplate('dashboard','default');
		$info->setIsHistorized(0);
		$info->setIsVisible(1);
		$info->setOrder(5);
		$info->save();
		
		$info = $this->getCmd(null, 'EVSE_AmpSetPointReadBack');
		if (!is_object($info)) {
			$info = new OpenEVSECmd();
			$info->setName(__('Consigne Demandée : ', __FILE__));
		}
		$info->setLogicalId('EVSE_AmpSetPointReadBack');
		$info->setEqLogic_id($this->getId());
		$info->setType('info');
		$info->setSubType('numeric');
		$info->setTemplate('dashboard','line');
		$info->setConfiguration('minValue', $this->getConfiguration("AMin"));
		$info->setConfiguration('maxValue', $this->getConfiguration("AMax"));
		$info->setIsHistorized(1);
		$info->setUnite('A');
		$info->setOrder(6);
		$info->save();
		
		$action = $this->getCmd(null, 'EVSE_AmpSetPointSlider');
		$AMin = $this->getConfiguration("AMin");
		$AMax = $this->getConfiguration("AMax");
		if (empty($AMax)) {
			$AMax = 6;
        }
		if (empty($AMin)) {
			$AMin = 6;
		}
		if (!is_object($action)) {
			$action = new OpenEVSECmd();
			$action->setLogicalId('EVSE_AmpSetPointSlider');
			$action->setName(__('Curseur Consigne', __FILE__));
		}
		$action->setType('action');
		$action->setSubType('slider');
	    $action->setConfiguration('stepValue', 1);
		$action->setConfiguration('minValue', $AMin);
		$action->setConfiguration('maxValue', $AMax);
		$action->setEqLogic_id($this->getId());
	    $action->setUnite('A');
		$action->setDisplay("showNameOndashboard",0);
		$action->setOrder(7);
		$action->save();
					
		$info = $this->getCmd(null, 'EVSE_State');
		if (!is_object($info)) {
			$info = new OpenEVSECmd();
			$info->setName(__('Etat : ', __FILE__));
		}
		$info->setLogicalId('EVSE_State');
		$info->setEqLogic_id($this->getId());
		$info->setType('info');
		$info->setSubType('string');
		$info->setTemplate('dashboard','default');
		$info->setIsHistorized(0);
		$info->setIsVisible(1);
		$info->setOrder(8);
		$info->save();
		
		$info = $this->getCmd(null, 'EVSE_Mode');
		if (!is_object($info)) {
			$info = new OpenEVSECmd();
			$info->setName(__('Mode : ', __FILE__));
		}
		$info->setLogicalId('EVSE_Mode');
		$info->setEqLogic_id($this->getId());
		$info->setType('info');
		$info->setSubType('string');
		$info->setTemplate('dashboard','default');
		$info->setIsHistorized(0);
		$info->setIsVisible(1);
		$info->setOrder(9);
		$info->save();
		$this->checkAndUpdateCmd('EVSE_Mode', 'Manuel');
		
		$action = $this->getCmd(null, 'EVSE_Start');
		if (!is_object($action)) {
			$action = new OpenEVSECmd();
			$action->setLogicalId('EVSE_Start');
			$action->setName(__('ON', __FILE__));
		}
		$action->setType('action');
		$action->setSubType('other');
		$action->setEqLogic_id($this->getId());
		$action->setOrder(10);
		$action->save();
		
		$action = $this->getCmd(null, 'EVSE_Stop');
		if (!is_object($action)) {
			$action = new OpenEVSECmd();
			$action->setLogicalId('EVSE_Stop');
			$action->setName(__('OFF', __FILE__));
		}
		$action->setType('action');
		$action->setSubType('other');
		$action->setEqLogic_id($this->getId());
		$action->setOrder(11);
		$action->save();
      
      	$Mode = $this->getConfiguration("Mode");
     	$action = $this->getCmd(null, 'EVSE_Pause');
		if (is_object($action)) {
        	if ($Mode == 1) {
				$action->remove();
              	log::add('OpenEVSE', 'debug','Suppression commande PAUSE (WIFI API)');
           	}
        } else {
        	if ($Mode == 0) {
  				$action = new OpenEVSECmd();
				$action->setLogicalId('EVSE_Pause');
				$action->setName(__('PAUSE', __FILE__));
          		$action->setType('action');
				$action->setSubType('other');
				$action->setEqLogic_id($this->getId());
				$action->setOrder(11);
          		$action->save();
          		log::add('OpenEVSE', 'debug','Ajout commande PAUSE (RAPI)');
            }
        }
		
		$action = $this->getCmd(null, 'EVSE_ModeMan');
		if (!is_object($action)) {
			$action = new OpenEVSECmd();
			$action->setLogicalId('EVSE_ModeMan');
			$action->setName(__('Man.', __FILE__));
		}
		$action->setType('action');
		$action->setSubType('other');
		$action->setEqLogic_id($this->getId());
		$action->setOrder(13);
		$action->save();
		
		$action = $this->getCmd(null, 'EVSE_ModeAuto');
		if (!is_object($action)) {
			$action = new OpenEVSECmd();
			$action->setLogicalId('EVSE_ModeAuto');
			$action->setName(__('Auto.', __FILE__));
		}
		$action->setType('action');
		$action->setSubType('other');
		$action->setEqLogic_id($this->getId());
		$action->setOrder(14);
		$action->save();
		
		$info = $this->getCmd(null, 'EVSE_PersoString');
		if (!is_object($info)) {
			$info = new OpenEVSECmd();
			$info->setName(__('Perso. Texte', __FILE__));
		}
		$info->setLogicalId('EVSE_PersoString');
		$info->setEqLogic_id($this->getId());
		$info->setType('info');
		$info->setSubType('string');
		$info->setTemplate('dashboard','default');
		$info->setIsHistorized(0);
		$info->setIsVisible(0);
		$info->setOrder(15);
		$info->save();
		
		$info = $this->getCmd(null, 'EVSE_PersoNumeric');
		if (!is_object($info)) {
			$info = new OpenEVSECmd();
			$info->setName(__('Perso. Num.', __FILE__));
		}
		$info->setLogicalId('EVSE_PersoNumeric');
		$info->setEqLogic_id($this->getId());
		$info->setType('info');
		$info->setSubType('numeric');
		$info->setTemplate('dashboard','line');
		$info->setIsHistorized(1);
		$info->setIsVisible(0);
		$info->setOrder(16);
		$info->save();
      
      	$info = $this->getCmd(null, 'EVSE_PersoBinary');
		if (!is_object($info)) {
			$info = new OpenEVSECmd();
			$info->setName(__('Perso. Bin.', __FILE__));
		}
		$info->setLogicalId('EVSE_PersoBinary');
		$info->setEqLogic_id($this->getId());
		$info->setType('info');
		$info->setSubType('binary');
		$info->setTemplate('dashboard','line');
		$info->setIsHistorized(1);
		$info->setIsVisible(0);
		$info->setOrder(17);
		$info->save();

		$refresh = $this->getCmd(null, 'refresh');
		if (!is_object($refresh)) {
			$refresh = new OpenEVSECmd();
			$refresh->setName(__('Rafraîchir', __FILE__));
		}
		$refresh->setEqLogic_id($this->getId());
		$refresh->setLogicalId('refresh');
		$refresh->setType('action');
		$refresh->setSubType('other');
		$refresh->setOrder(50);
		$refresh->save();
      
      	
    }

    public function preUpdate() {

    }

    public function postUpdate() {
		$cmd = $this->getCmd(null, 'refresh'); // On recherche la commande refresh de l’équipement
		if (is_object($cmd)) { //elle existe et on lance la commande
			 $cmd->execCmd();
		}
    }

    public function preRemove() {
       
    }

    public function postRemove() {
        
    }
	
    /*
     * Non obligatoire mais permet de modifier l'affichage du widget si vous en avez besoin
      public function toHtml($_version = 'dashboard') {

      }
     */

    /*
     * Non obligatoire mais ca permet de déclencher une action après modification de variable de configuration
    public static function postConfig_<Variable>() {
    }
     */

    /*
     * Non obligatoire mais ca permet de déclencher une action avant modification de variable de configuration
    public static function preConfig_<Variable>() {
    }
     */

    /*     * **********************Getteur Setteur*************************** */
}

class OpenEVSECmd extends cmd {
    /*     * *************************Attributs****************************** */


    /*     * ***********************Methode static*************************** */


    /*     * *********************Methode d'instance************************* */

    /*
     * Non obligatoire permet de demander de ne pas supprimer les commandes même si elles ne sont pas dans la nouvelle configuration de l'équipement envoyé en JS
      public function dontRemoveCmd() {
      return true;
      }
     */

    public function execute($_options = array()) {
		$eqlogic = $this->getEqLogic();
			switch ($this->getLogicalId()) {		
				case 'EVSE_AmpSetPointSlider':
					$info = $eqlogic->SetSliderSetPoint($_options['slider']/1);
					$eqlogic->checkAndUpdateCmd('EVSE_AmpSetPointReadBack', $info); 
					break;
				case 'EVSE_Start':
					$cmd = $eqlogic->SetStartStop('Start');
					$info = $eqlogic->GetData();					
					break;
				case 'EVSE_Stop':
					$cmd = $eqlogic->SetStartStop('Stop');
					$info = $eqlogic->GetData();
					break;
				case 'EVSE_Pause':
					$cmd = $eqlogic->SetStartStop('Pause');
					$info = $eqlogic->GetData();
					break;
				case 'EVSE_ModeMan':
					$cmd = $eqlogic->SetMode('Man');
					$info = $eqlogic->GetData();
					break;
				case 'EVSE_ModeAuto':
					$cmd = $eqlogic->SetMode('Auto');
					$info = $eqlogic->GetData();
					break;
				case 'refresh':
					$info = $eqlogic->GetData();
					break;					
		}
    }
    /*     * **********************Getteur Setteur*************************** */
}
