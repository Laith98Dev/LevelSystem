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

use pocketmine\console\ConsoleCommandSender;
use pocketmine\player\Player;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat as TF;

class DataMgr 
{
	public function __construct(
		private Main $plugin
		){
		// NOOP
	}
	
	public function getPlugin(){
		return $this->plugin;
	}
	
	public function getPlayers(){
		$players = [];
		
		foreach (scandir($this->getPlugin()->getDataFolder() . "players") as $item){
			if(in_array($item, [".", ".."]))
				continue;
			$players[] = str_replace(".yml", "", $item);
		}
		
		return $players;
	}
	
	public function checkAccount(Player $player){
		if(!is_file($this->getPlugin()->getDataFolder() . "players/" . strtolower($player->getName()) . ".yml")){
			$cfg = new Config($this->getPlugin()->getDataFolder() . "players/" . strtolower($player->getName()) . ".yml", Config::YAML, [
				"Level" => 1,
				"XP" => 0,
				"addXP" => 0,
				"nextLevelXP" => 0
			]);
			
			// $add = (100 * $cfg->get("Level") * 5) / 2;
			// $nextLevelXP = ($add * $cfg->get("Level") * 100) / ($cfg->get("Level") * 5);
			
			$add = (50 * $cfg->get("Level") / 2);
			$nextLevelXP = ($add * $cfg->get("Level") * 100) / ($cfg->get("Level") * 4);
			
			$cfg->set("addXP", $add);
			$cfg->set("nextLevelXP", $nextLevelXP);
			$cfg->save();
			
			$this->getPlugin()->getLogger()->info("Creating new account for '" . strtolower($player->getName()) . "'");
		}
	}
	
	public function getPlayerData(string|Player $player): ?Config{
		if($player instanceof Player){
			if(is_file($this->getPlugin()->getDataFolder() . "players/" . strtolower($player->getName()) . ".yml")){
				return new Config($this->getPlugin()->getDataFolder() . "players/" . strtolower($player->getName()) . ".yml", Config::YAML);
			}
		} else {
			if(is_file($this->getPlugin()->getDataFolder() . "players/" . strtolower($player) . ".yml")){
				return new Config($this->getPlugin()->getDataFolder() . "players/" . strtolower($player) . ".yml", Config::YAML);
			}
		}
		return null;
	}
	
	public function getAddXpCount(string|Player $player): ?int{
		if(($data = $this->getPlayerData($player)) !== null){
			return $data->getAll()["addXP"];
		}
		
		return null;
	}
	
	public function getLevel(string|Player $player): ?int{
		if(($data = $this->getPlayerData($player)) !== null){
			return $data->getAll()["Level"];
		}
		
		return null;
	}
	
	public function addLevel(string|Player $player, int $add): bool{
		if(($data = $this->getPlayerData($player)) !== null){
			$level = $data->get("Level");
			
			$cfg = new Config($this->getPlugin()->getDataFolder() . "settings.yml", Config::YAML);
			if($level >= intval($cfg->get("MaxLevel")))
				return false;
			
			$level += $add;
			$data->set("Level", $level);
			$data->save();
			return true;
		}
		
		return false;
	}
	
	public function setLevel(string|Player $player, int $new): bool{
		if(($data = $this->getPlayerData($player)) !== null){
			
			$cfg = new Config($this->getPlugin()->getDataFolder() . "settings.yml", Config::YAML);
			if($data->get("Level") >= intval($cfg->get("MaxLevel")))
				return false;
			
			$data->set("Level", $new);
			$data->save();
			return true;
		}
		
		return false;
	}
	
	public function getXP(string|Player $player): ?int{
		if(($data = $this->getPlayerData($player)) !== null){
			return $data->getAll()["XP"];
		}
		
		return null;
	}
	
	public function setXP(string|Player $player, int $new): bool{
		if(($data = $this->getPlayerData($player)) !== null){
			$xp = $data->get("XP");
			
			$cfg = new Config($this->getPlugin()->getDataFolder() . "settings.yml", Config::YAML);
			if(intval($data->get("Level")) >= intval($cfg->get("MaxLevel")))
				return false;
			
			$data->set("XP", $new);
			$data->save();
			return true;
		}
		
		return false;
	}
	
	public function addXP(string|Player $player, int $add): bool{
		//var_dump("add xp function <br>");
		if(($data = $this->getPlayerData($player)) !== null){
			//var_dump("data not null");
			$xp = $data->get("XP");
			
			$cfg = new Config($this->getPlugin()->getDataFolder() . "settings.yml", Config::YAML);
			if(intval($data->get("Level")) >= intval($cfg->get("MaxLevel")))
				return false;
			
			$xp += $add;
			$data->set("XP", $xp);
			$data->save();
			
			if($this->getXP($player) >= $this->getNextLevelXP($player)){
				$this->prepareNewLevel($player, ($this->getLevel($player) + 1));
			}
			
			return true;
		}
		
		return false;
	}
	
	public function getNextLevelXP(string|Player $player): ?int{
		if(($data = $this->getPlayerData($player)) !== null){
			return $data->getAll()["nextLevelXP"];
		}
		
		return null;
	}
	
	public function setNextLevelXP(string|Player $player, int $new): bool{
		if(($data = $this->getPlayerData($player)) !== null){
			$data->set("nextLevelXP", $new);
			$data->save();
			return true;
		}
		
		return false;
	}
		
	public function prepareNewLevel(string|Player $player, int $newLevel): bool{
		if(($data = $this->getPlayerData($player)) !== null){
			
			$cfg = new Config($this->getPlugin()->getDataFolder() . "settings.yml", Config::YAML);
			if($newLevel > intval($cfg->get("MaxLevel")))
				return false;
			
			//$add = (100 * $newLevel * 5) / 2;
			//$add = (100 * $newLevel * 4) / 2;
			//$newNextLevel = ($add * $newLevel * 100) / ($newLevel * 5);
			//$newNextLevel = ($add * $newLevel * 100) / ($newLevel * 4);
			
			$add = (50 * $newLevel / 2);
			$newNextLevel = ($add * $newLevel * 100) / ($newLevel * 4);
			
			if($player instanceof Player){
				if(($msg = $cfg->get("level." . $newLevel . ".message"))){
					$player->sendMessage(str_replace(["{newLvl}", "{oldLvl}", "{player}", "&"], [$newLevel, $this->getLevel($player), $player->getName(), TF::ESCAPE], $msg));
				} else {
					$player->sendMessage(str_replace(["{newLvl}", "{oldLvl}", "{player}", "&"], [$newLevel, $this->getLevel($player), $player->getName(), TF::ESCAPE], $cfg->get("default-level-message")));
				}

				if($cfg->get("new.level.reward") === true){
					if(($cmds = $cfg->get("new.level.reward.commands")) && is_array($cmds)){
						foreach ($cmds as $cmd){
							$this->plugin->getServer()->dispatchCommand(new ConsoleCommandSender($this->plugin->getServer(), $this->plugin->getServer()->getLanguage()), str_replace(["&", "{player}"], [TF::ESCAPE, '"' . $player->getName() . '"'], $cmd));
						}
					}
				}

				// later...
				// $lvl = $this->getLevel($player);
				// $player->setNameTag(str_replace(["{lvl}", ($newLevel - 1)], [$lvl, $lvl], $player->getNameTag()));
			} else {
				$p = $this->getPlugin()->getServer()->getPlayerByPrefix($player);
				if($p !== null){
					if(($msg = $cfg->get("level." . $newLevel . ".message"))){
						$p->sendMessage(str_replace(["{newLvl}", "{oldLvl}", "{player}", "&"], [$newLevel, $this->getLevel($player), $p, TF::ESCAPE], $msg));
					} else {
						$p->sendMessage(str_replace(["{newLvl}", "{oldLvl}", "{player}", "&"], [$newLevel, $this->getLevel($p), $p, TF::ESCAPE], $cfg->get("default-level-message")));
					}

					if($cfg->get("new.level.reward") === true){
						if(($cmds = $cfg->get("new.level.reward.commands")) && is_array($cmds)){
							foreach ($cmds as $cmd){
								$this->plugin->getServer()->dispatchCommand(new ConsoleCommandSender($this->plugin->getServer(), $this->plugin->getServer()->getLanguage()), str_replace(["&", "{player}"], [TF::ESCAPE, '"' . $p . '"'], $cmd));
							}
						}
					}

					// $lvl = $this->getLevel($player);
					// $player->setNameTag(str_replace(["{lvl}", ($newLevel - 1)], [$lvl, $lvl], $player->getNameTag()));
					// $p->sendMessage(TF::YELLOW . "Congratulations, you have reached level " . $newLevel
				}
			}
			
			$data->set("Level", $newLevel);
			$data->set("addXP", $add);
			$data->set("nextLevelXP", $newNextLevel);
			$data->set("XP", 0);
			$data->save();
			return true;
		}
		
		return false;
	}
}
