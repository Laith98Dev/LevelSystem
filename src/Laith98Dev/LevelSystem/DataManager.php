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
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat as TF;

class DataManager 
{
	/** @var Main */
	private $plugin;
	
	public function __construct(Main $plugin){
		$this->plugin = $plugin;
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
	
	public function getPlayerData($player): ?Config{
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
	
	public function getAddXpCount($player): ?int{
		if(($data = $this->getPlayerData($player)) !== null){
			return $data->getAll()["addXP"];
		}
		
		return null;
	}
	
	public function getLevel($player): ?int{
		if(($data = $this->getPlayerData($player)) !== null){
			return $data->getAll()["Level"];
		}
		
		return null;
	}
	
	public function addLevel($player, int $add): bool{
		if(($data = $this->getPlayerData($player)) !== null){
			$level = $data->get("Level");
			
			$cfg = new Config($this->getPlugin()->getDataFolder() . "settings.yml", Config::YAML);
			if($level >= $cfg->get("MaxLevel"))
				return false;
			
			$new = ($level + $add);
			$data->set("Level", $new);
			$data->save();
			return true;
		}
		
		return false;
	}
	
	public function setLevel($player, int $new): bool{
		if(($data = $this->getPlayerData($player)) !== null){
			
			$cfg = new Config($this->getPlugin()->getDataFolder() . "settings.yml", Config::YAML);
			if($data->get("Level") >= $cfg->get("MaxLevel"))
				return false;
			
			$data->set("Level", $new);
			$data->save();
			return true;
		}
		
		return false;
	}
	
	public function getXP($player): ?int{
		if(($data = $this->getPlayerData($player)) !== null){
			return $data->getAll()["XP"];
		}
		
		return null;
	}
	
	public function setXP($player, int $new): bool{
		if(($data = $this->getPlayerData($player)) !== null){
			$xp = $data->get("XP");
			
			$cfg = new Config($this->getPlugin()->getDataFolder() . "settings.yml", Config::YAML);
			if($data->get("Level") >= $cfg->get("MaxLevel"))
				return false;
			
			$data->set("XP", $new);
			$data->save();
			return true;
		}
		
		return false;
	}
	
	public function addXP($player, int $add): bool{
		//var_dump("add xp function <br>");
		if(($data = $this->getPlayerData($player)) !== null){
			//var_dump("data not null");
			$xp = $data->get("XP");
			
			$cfg = new Config($this->getPlugin()->getDataFolder() . "settings.yml", Config::YAML);
			if($data->get("Level") >= $cfg->get("MaxLevel"))
				return false;
			
			$new = ($xp + $add);
			$data->set("XP", $new);
			$data->save();
			
			if($this->getXP($player) >= $this->getNextLevelXP($player)){
				$this->prepareNewLevel($player, ($this->getLevel($player) + 1));
			}
			
			return true;
		}
		
		return false;
	}
	
	public function getNextLevelXP($player): ?int{
		if(($data = $this->getPlayerData($player)) !== null){
			return $data->getAll()["nextLevelXP"];
		}
		
		return null;
	}
	
	public function setNextLevelXP($player, int $new): bool{
		if(($data = $this->getPlayerData($player)) !== null){
			$data->set("nextLevelXP", $new);
			$data->save();
			return true;
		}
		
		return false;
	}
		
	public function prepareNewLevel($player, int $newLevel): bool{
		if(($data = $this->getPlayerData($player)) !== null){
			
			$cfg = new Config($this->getPlugin()->getDataFolder() . "settings.yml", Config::YAML);
			if($newLevel > $cfg->get("MaxLevel"))
				return false;
			
			//$add = (100 * $newLevel * 5) / 2;
			//$add = (100 * $newLevel * 4) / 2;
			//$newNextLevel = ($add * $newLevel * 100) / ($newLevel * 5);
			//$newNextLevel = ($add * $newLevel * 100) / ($newLevel * 4);
			
			$add = (50 * $newLevel / 2);
			$newNextLevel = ($add * $newLevel * 100) / ($newLevel * 4);
			
			if($player instanceof Player){
				$player->sendMessage(TF::YELLOW . "Congratulations, you have reached level " . $newLevel);
				$lvl = $this->getLevel($player);
				$player->setNameTag(str_replace(["{lvl}", ($newLevel - 1)], [$lvl, $lvl], $player->getNameTag()));
			} else {
				$p = $this->getPlugin()->getServer()->getPlayer($player);
				if($p !== null){
					// $lvl = $this->getLevel($player);
					// $player->setNameTag(str_replace(["{lvl}", ($newLevel - 1)], [$lvl, $lvl], $player->getNameTag()));
					// $p->sendMessage(TF::YELLOW . "Congratulations, you have reached level " . $newLevel);
					$p->sendMessage(str_replace(["&", "{newLevel}", "{nextLevelXP}"], [TF::ESCAPE, $newLevel, $newNextLevel], $cfg->get("new-level-message")));
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
