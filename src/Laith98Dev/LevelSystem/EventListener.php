<?php

namespace Laith98Dev\LevelSystem;

/*  
 *  A plugin for PocketMine-MP.
 *  
 *	 _           _ _   _    ___   ___  _____             
 *	| |         (_) | | |  / _ \ / _ \|  __ \            
 *	| |     __ _ _| |_| |_| (_) | (_) | |  | | _____   __
 *	| |    / _` | | __| '_ \__, |> _ <| |  | |/ _ \ \ / /
 *	| |___| (_| | | |_| | | |/ /| (_) | |__| |  __/\ V / 
 *	|______\__,_|_|\__|_| |_/_/  \___/|_____/ \___| \_/  
 *	
 *	Copyright (C) 2021 Laith98Dev
 *  
 *	Youtube: Laith Youtuber
 *	Discord: Laith98Dev#0695
 *	Gihhub: Laith98Dev
 *	Email: help@laithdev.tk
 *	Donate: https://paypal.me/Laith113
 *
 *	This program is free software: you can redistribute it and/or modify
 *	it under the terms of the GNU General Public License as published by
 *	the Free Software Foundation, either version 3 of the License, or
 *	(at your option) any later version.
 *
 *	This program is distributed in the hope that it will be useful,
 *	but WITHOUT ANY WARRANTY; without even the implied warranty of
 *	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *	GNU General Public License for more details.
 *
 *	You should have received a copy of the GNU General Public License
 *	along with this program.  If not, see <http://www.gnu.org/licenses/>.
 * 	
 */

use pocketmine\Player;
use pocketmine\event\Listener;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat as TF;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\entity\{EntityDamageEvent, EntityDamageByEntityEvent};

class EventListener implements Listener 
{
	/** @var Main */
	private $plugin;
	
	public function __construct(Main $plugin){
		$this->plugin = $plugin;
	}
	
	public function getPlugin(){
		return $this->plugin;
	}
	
	public function getDataFolder(){
		return $this->plugin->getDataFolder();
	}
	
	public function onJoin(PlayerJoinEvent $event){
		$player = $event->getPlayer();
		if($player instanceof Player){
			$this->getPlugin()->getDataManager()->checkAccount($player);
			// soon 
			// $lvl = $this->getPlugin()->getDataManager()->getLevel($player);
			// $player->setNameTag(str_replace("{lvl}", $lvl, $player->getNameTag()));
		}
	}
	
	public function onPlace(BlockPlaceEvent $event){
		$player = $event->getPlayer();
		$block = $event->getBlock();
		if($player instanceof Player){
			$cfg = new Config($this->getDataFolder() . "settings.yml", Config::YAML);
			if($cfg->get("plugin-enable") === true){
				if($cfg->get("add-xp-by-build") === true){
					if(($c = mt_rand(0, 300)) < 150 && $c > 50 && mt_rand(0, 50) < 20){// random
						if($this->getPlugin()->getDataManager()->addXP($player, $this->getPlugin()->getDataManager()->getAddXpCount($player))){
							$player->sendPopup(TF::YELLOW . "+" . $this->getPlugin()->getDataManager()->getAddXpCount($player) . " XP");
						}
					}
				}
			}
		}
	}
	
	public function onBreak(BlockBreakEvent $event){
		$player = $event->getPlayer();
		$block = $event->getBlock();
		if($player instanceof Player){
			$cfg = new Config($this->getDataFolder() . "settings.yml", Config::YAML);
			if($cfg->get("plugin-enable") && $cfg->get("plugin-enable") === true){
				if($cfg->get("add-xp-by-destroy") && $cfg->get("add-xp-by-destroy") === true){
					if(($c = mt_rand(0, 300)) < 150 && $c > 50 && mt_rand(0, 50) < 20){// random
						if($this->getPlugin()->getDataManager()->addXP($player, $this->getPlugin()->getDataManager()->getAddXpCount($player))){
							$player->sendPopup(TF::YELLOW . "+" . $this->getPlugin()->getDataManager()->getAddXpCount($player) . " XP");
						}
					}
				}
			}
		}
	}
	
	public function onDeath(PlayerDeathEvent $event){
		$player = $event->getPlayer();
		if($player instanceof Player){
			$cfg = new Config($this->getDataFolder() . "settings.yml", Config::YAML);
			if($cfg->get("plugin-enable") === true){
				if($cfg->get("add-xp-by-kill") === true){
					if($cfg->get("kill-with-death-screen") === true){
						var_dump("death here hi \n");
						if(($c = mt_rand(0, 300)) < 150 && $c > 50 && mt_rand(0, 50) < 20){// random
							if($this->getPlugin()->getDataManager()->addXP($player, $this->getPlugin()->getDataManager()->getAddXpCount($player))){
								$player->sendPopup(TF::YELLOW . "+" . $this->getPlugin()->getDataManager()->getAddXpCount($player) . " XP");
							}
						}
					}
				}
			}
		}
	}
	
	public function onDamage(EntityDamageEvent $event){
		$entity = $event->getEntity();
		if($entity instanceof Player){
			if($event instanceof EntityDamageByEntityEvent && ($damager = $event->getDamager()) instanceof Player){
				$cfg = new Config($this->getDataFolder() . "settings.yml", Config::YAML);
				if($cfg->get("plugin-enable") === true){
					if($cfg->get("add-xp-by-kill") === true){
						if($cfg->get("kill-with-death-screen") === false){
							if($entity->getHealth() <= $event->getFinalDamage()){
								var_dump("Finaly damage hi \n");
								if(($c = mt_rand(0, 300)) < 150 && $c > 50 && mt_rand(0, 50) < 20){// random
									if($this->getPlugin()->getDataManager()->addXP($damager, $this->getPlugin()->getDataManager()->getAddXpCount($player))){
										$damager->sendPopup(TF::YELLOW . "+" . $this->getPlugin()->getDataManager()->getAddXpCount($damager) . " XP");
									}
								}
							}
						}
					}
				}
			}
		}
	}
	
	public function onChat(PlayerChatEvent $event){
		$player = $event->getPlayer();
		$message = $event->getMessage();
		if($player instanceof Player){
			$cfg = new Config($this->getDataFolder() . "settings.yml", Config::YAML);
			if($cfg->get("plugin-enable") === true){
				if($cfg->get("add-xp-by-chat") === true){
					if(($c = mt_rand(0, 300)) < 150 && $c > 50 && mt_rand(0, 50) < 20){// random
						if($this->getPlugin()->getDataManager()->addXP($player, $this->getPlugin()->getDataManager()->getAddXpCount($player))){
							$player->sendPopup(TF::YELLOW . "+" . $this->getPlugin()->getDataManager()->getAddXpCount($player) . " XP");
						}
					}
				}
				
				// chat format
				$lvl = $this->getPlugin()->getDataManager()->getLevel($player);
				if(!$event->isCancelled() && $cfg->get("edit-chat-format") === true){
					if($this->getPlugin()->pureChat !== null){
												
						$levelName = $this->getPlugin()->pureChat->getConfig()->get("enable-multiworld-chat") ? $player->getLevel()->getName() : null;
						$chatFormat = $this->getPlugin()->pureChat->getChatFormat($player, $message, $levelName);
						$chatFormat = str_replace("{lvl}", $lvl, $chatFormat);
						//var_dump("Befor: " . $event->getFormat() . "\n");
						//var_dump("1: " . $chatFormat . "\n");
						//var_dump("2: " . $chatFormat . "\n");
						
						// idk but not work with setFormat()
						//$event->setFormat($chatFormat); 
						$event->setCancelled();
						$this->getPlugin()->getServer()->broadcastMessage($chatFormat);
					} else {
						if($cfg->get("chatFormat") && $cfg->get("chatFormat") !== ""){
							$chatFormat = str_replace(["{name}", "{lvl}", "{msg}", "&"], [$player->getName(), $lvl, $message, TF::ESCAPE], $cfg->get("chatFormat"));
							//$event->setFormat($chatFormat);
							$this->getPlugin()->getServer()->broadcastMessage($chatFormat);
						}
					}
				}
			}
		}
	}
}
