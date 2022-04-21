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
 *	Github: Laith98Dev
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

use pocketmine\player\Player;
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
	public function __construct(
		private Main $plugin
		){
		// NOOP
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
	
	/**
	 * @priority HIGHEST
	 */
	public function onPlace(BlockPlaceEvent $event): void{
		$player = $event->getPlayer();
		$block = $event->getBlock();
		if($event->isCancelled())
			return;
		
		// $this->plugin->getScheduler()->scheduleDelayedTask(new PrepareTask($this->plugin, $player, $block, 1), 1 * 20);

		if($player instanceof Player){
			var_dump("Event Work\n");
			$cfg = new Config($this->plugin->getDataFolder() . "settings.yml", Config::YAML);
			if($cfg->get("plugin-enable") === true){
				if($cfg->get("add-xp-by-build") === true && in_array($block->getId(), $cfg->get("blocks-list", []))){
					if(mt_rand(0, 200) < 120 && mt_rand(0, 1) == 1 && mt_rand(0, 1) == 0 && mt_rand(0, 3) == 2){// random
						if($this->plugin->getDataManager()->addXP($player, $this->plugin->getDataManager()->getAddXpCount($player))){
							$player->sendPopup(TF::YELLOW . "+" . $this->plugin->getDataManager()->getAddXpCount($player) . " XP");
						}
					}
				}
			}
		}
	}
	
	/**
	 * @priority HIGHEST
	 */
	public function onBreak(BlockBreakEvent $event): void{
		$player = $event->getPlayer();
		$block = $event->getBlock();
		if($event->isCancelled())
			return;
		
		// $this->plugin->getScheduler()->scheduleDelayedTask(new PrepareTask($this->plugin, $player, $block, 2), 1 * 20);

		if($player instanceof Player){
			var_dump("Event Work\n");
			$cfg = new Config($this->plugin->getDataFolder() . "settings.yml", Config::YAML);
			if($cfg->get("plugin-enable") && $cfg->get("plugin-enable") === true){
				if($cfg->get("add-xp-by-destroy") && $cfg->get("add-xp-by-destroy") === true && in_array($block->getId(), $cfg->get("blocks-list", []))){
					if(mt_rand(0, 200) < 120 && mt_rand(0, 1) == 1 && mt_rand(0, 1) == 0 && mt_rand(0, 3) == 2){// random
						if($this->plugin->getDataManager()->addXP($player, $this->plugin->getDataManager()->getAddXpCount($player))){
							$player->sendPopup(TF::YELLOW . "+" . $this->plugin->getDataManager()->getAddXpCount($player) . " XP");
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
						//var_dump("death here hi \n");
						if(mt_rand(0, 200) < 120 && mt_rand(0, 1) == 1 && mt_rand(0, 1) == 0 && mt_rand(0, 3) == 2){// random
							if($this->getPlugin()->getDataManager()->addXP($player, $this->getPlugin()->getDataManager()->getAddXpCount($player))){
								$player->sendPopup(TF::YELLOW . "+" . $this->getPlugin()->getDataManager()->getAddXpCount($player) . " XP");
							}
						}
					}
				}
			}
		}
	}
	
	/**
	 * @priority HIGHEST
	 */
	public function onDamage(EntityDamageEvent $event){
		$entity = $event->getEntity();

		if($event->isCancelled())
			return;
		
		if($entity instanceof Player){
			if($event instanceof EntityDamageByEntityEvent && ($damager = $event->getDamager()) instanceof Player){
				$cfg = new Config($this->getDataFolder() . "settings.yml", Config::YAML);
				if($cfg->get("plugin-enable") === true){
					if($cfg->get("add-xp-by-kill") === true){
						if($cfg->get("kill-with-death-screen") === false){
							if($entity->getHealth() <= $event->getFinalDamage()){
								//var_dump("Finaly damage hi \n");
								if(mt_rand(0, 200) < 120 && mt_rand(0, 1) == 1 && mt_rand(0, 1) == 0 && mt_rand(0, 3) == 2){// random
									if($this->getPlugin()->getDataManager()->addXP($damager, $this->getPlugin()->getDataManager()->getAddXpCount($damager))){
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
	
	/**
	 * @priority HIGHEST
	 */
	public function onChat(PlayerChatEvent $event){
		$player = $event->getPlayer();
		$message = $event->getMessage();

		if($event->isCancelled())
			return;

		if($player instanceof Player){
			$cfg = new Config($this->getDataFolder() . "settings.yml", Config::YAML);
			if($cfg->get("plugin-enable") === true){
				if($cfg->get("add-xp-by-chat") === true){
					if(mt_rand(0, 200) < 120 && mt_rand(0, 1) == 1 && mt_rand(0, 1) == 0 && mt_rand(0, 3) == 2){// random
						if($this->getPlugin()->getDataManager()->addXP($player, $this->getPlugin()->getDataManager()->getAddXpCount($player))){
							$player->sendPopup(TF::YELLOW . "+" . $this->getPlugin()->getDataManager()->getAddXpCount($player) . " XP");
						}
					}
				}
				
				// chat format
				$lvl = $this->getPlugin()->getDataManager()->getLevel($player);
				if(!$event->isCancelled() && $cfg->get("edit-chat-format") === true){
					if($this->getPlugin()->pureChat !== null){
												
						$levelName = $this->getPlugin()->pureChat->getConfig()->get("enable-multiworld-chat") ? $player->getWorld()->getFolderName() : null;
						$chatFormat = $this->getPlugin()->pureChat->getChatFormat($player, $message, $levelName);
						$chatFormat = str_replace("{lvl}", $lvl, $chatFormat);
						//var_dump("Befor: " . $event->getFormat() . "\n");
						//var_dump("1: " . $chatFormat . "\n");
						//var_dump("2: " . $chatFormat . "\n");
						
						// idk but not work with setFormat()
						//$event->setFormat($chatFormat); 
						$event->cancel();
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
