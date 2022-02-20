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

use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;

use pocketmine\player\Player;
use pocketmine\block\Block;
use pocketmine\block\BlockLegacyIds;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat as TF;

use pocketmine\command\{Command, CommandSender};

use jojoe77777\FormAPI\SimpleForm;
use jojoe77777\FormAPI\CustomForm;
use jojoe77777\FormAPI\ModalForm;

class Main extends PluginBase 
{
	/** @var DataManager */
	public $dataManager;
	
	public $saveSession = [];
	
	/** @var Plugin|null */
	public $pureChat;
	
	public function onEnable(): void{
		@mkdir($this->getDataFolder());
		@mkdir($this->getDataFolder() . "players");
		
		if(!is_file($this->getDataFolder() . "settings.yml")){
			(new Config($this->getDataFolder() . "settings.yml", Config::YAML, [
				"plugin-enable" => true,
				"chatFormat" => "&c[&e{lvl}&c] &r{name} &7> &r{msg}",
				"add-xp-by-build" => true,
				"add-xp-by-destroy" => true,
				"add-xp-by-kill" => true,
				"add-xp-by-chat" => true,
				"kill-with-death-screen" => true,
				"edit-chat-format" => true,
				"blocks-list" => [BlockLegacyIds::STONE, BlockLegacyIds::DIRT],// List of blocks that give XP
				"new-level-message" => "&eCongratulations, you have reached level {newLevel}",
				"MaxLevel" => 100
			]));
		}
		
		$this->fixConfig();
		
		$this->dataManager = new DataMgr($this);
		
		$this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
		
		$this->pureChat = $this->getServer()->getPluginManager()->getPlugin("PureChat");
	}
	
	public function getDataManager(){
		return $this->dataManager;
	}
	
	public function fixConfig(){
		$cfg = new Config($this->getDataFolder() . "settings.yml", Config::YAML);
		$all = $cfg->getAll();
		$index = [
			"plugin-enable" => true,
			"chatFormat" => "&c[&e{lvl}&c] &r{name} &7> &r{msg}",
			"add-xp-by-build" => true,
			"add-xp-by-destroy" => true,
			"add-xp-by-kill" => true,
			"add-xp-by-chat" => true,
			"kill-with-death-screen" => true,
			"edit-chat-format" => true,
			"blocks-list" => [BlockLegacyIds::STONE, BlockLegacyIds::DIRT],// List of blocks that give XP
			"new-level-message" => "&eCongratulations, you have reached level {newLevel}",
			"MaxLevel" => 100
		];
		
		foreach ($index as $key => $value){
			if(!isset($all[$key])){
				$cfg->set($key, $value);
			}
		}
		
		$cfg->save();
	}
	
	public function onCommand(CommandSender $sender, Command $cmd, string $cmdLabel, array $args): bool{
		if($cmd->getName() == "ls"){// LS
			if($sender instanceof Player){
				if($sender->hasPermission("ls.cmd.staff")){
					$this->OpenMainForm($sender);
				} else {
					$this->OpenPlayerForm($sender, $sender->getName());
				}
			} else {
				$sender->sendMessage("run command in-game only!");
				return false;
			}
		}
		
		return true;
	}
	
	public function OpenMainForm(Player $player){
		$form = new SimpleForm(function (Player $player, int $data = null){
			if($data === null)
				return false;
			
			switch ($data){
				case 0:
					$this->OpenPlayersForm($player);
				break;
				
				case 1:
					$this->OpenSettingsForm($player);
				break;
			}
		});
		$form->setTitle("LevelSystem");
		$form->addButton("Players");
		$form->addButton("Settings");
		$form->sendToPlayer($player);
		return $form;
	}
	
	public function OpenPlayersForm(Player $player){
		$form = new SimpleForm(function (Player $player, int $data = null){
			if($data === null)
				return false;
			
			if(!isset($this->saveSession[$player->getName()])){
				$player->sendMessage(TF::RED . "Session time out please try again!");
				return false;
			}
			
			$players = $this->saveSession[$player->getName()]["players"];
			
			if(isset($players[$data])){
				$this->OpenPlayerForm($player, $players[$data]);
			}
			
			unset($this->saveSession[$player->getName()]);
		});
		
		$form->setTitle("LevelSystem");
		$players = [];
		foreach ($this->getDataManager()->getPlayers() as $p){
			$players[] = $p;
			$form->addButton($p);
		}
		
		if(count($players) === 0){
			$form->addButton("Exit");
		} else {
			$this->saveSession[$player->getName()] = ["players" => $players, "player" => null];
		}
		$form->sendToPlayer($player);
		return $form;
	}
	
	public function OpenPlayerForm(Player $player, string $player_){
		//$this->saveSession[$player->getName()]["player"] = $player_;
		$form = new SimpleForm(function (Player $player, int $data = null){
			if($data === null)
				return false;
			
			switch ($data){
				case 0:
					if($player->hasPermission("ls.cmd.staff")){
						$this->OpenPlayersForm($player);
					}
				break;
			}
		});
		$form->setTitle($player_ . " info");
		$form->setContent("\n - " . TF::YELLOW . "Level: " . TF::RESET . $this->getDataManager()->getLevel($player_) . TF::GREEN . " (" . $this->getDataManager()->getXP($player_) . TF::GRAY . "/" . TF::RESET . $this->getDataManager()->getNextLevelXP($player_) . TF::GREEN . ")");
		$form->addButton($player->hasPermission("ls.cmd.staff") ? "Back" : "Exit");
		$form->sendToPlayer($player);
		return $form;
	}
	
	public function OpenSettingsForm(Player $player){
		$form = new CustomForm(function (Player $player, array $data = null){
			if($data === null)
				return false;
			
			$cfg = new Config($this->getDataFolder() . "settings.yml", Config::YAML);
			$a = $cfg->get("plugin-enable");
			$b = $cfg->get("add-xp-by-build");
			$c = $cfg->get("add-xp-by-destroy");
			$d = $cfg->get("add-xp-by-kill");
			$e = $cfg->get("add-xp-by-chat");
			$f = $cfg->get("kill-with-death-screen");
			$g = $cfg->get("edit-chat-format");
			$h = $cfg->get("chatFormat");
			$i = $cfg->get("new-level-message");
			$j = intval($cfg->get("MaxLevel"));
			
			if($data[0] !== $a){
				$cfg->set("plugin-enable", $data[0]);
				$cfg->save();
			}
			
			if($data[1] !== $b){
				$cfg->set("add-xp-by-build", $data[1]);
				$cfg->save();
			}
			
			if($data[2] !== $c){
				$cfg->set("add-xp-by-destroy", $data[2]);
				$cfg->save();
			}
			
			if($data[3] !== $d){
				$cfg->set("add-xp-by-kill", $data[3]);
				$cfg->save();
			}
			
			if($data[4] !== $e){
				$cfg->set("add-xp-by-chat", $data[4]);
				$cfg->save();
			}
			
			if($data[5] !== $f){
				$cfg->set("kill-with-death-screen", $data[5]);
				$cfg->save();
			}
			
			if($data[6] !== $g){
				$cfg->set("edit-chat-format", $data[6]);
				$cfg->save();
			}
			
			if($data[7] !== $h){
				$cfg->set("chatFormat", $data[7]);
				$cfg->save();
			}
			
			if($data[8] !== $i){
				$cfg->set("new-level-message", $data[8]);
				$cfg->save();
			}
			
			if($data[9] !== $j){
				$cfg->set("MaxLevel", intval($data[9]));
				$cfg->save();
			}
			
			$player->sendMessage(TF::YELLOW . "Changes saved");
		});
		
		$cfg = new Config($this->getDataFolder() . "settings.yml", Config::YAML);
		
		$form->setTitle("LevelSystem");
		
		if($cfg->get("plugin-enable") === true){
			$form->addToggle("Plugin enable: ", true);
		} else {
			$form->addToggle("Plugin enable: ", false);
		}
		
		if($cfg->get("add-xp-by-build") === true){
			$form->addToggle("Add XP-Build: ", true);
		} else {
			$form->addToggle("Add XP-Build: ", false);
		}
		
		if($cfg->get("add-xp-by-destroy") === true){
			$form->addToggle("Add XP-Destroy: ", true);
		} else {
			$form->addToggle("Add XP-Destroy: ", false);
		}
		
		if($cfg->get("add-xp-by-kill") === true){
			$form->addToggle("Add XP-Kill: ", true);
		} else {
			$form->addToggle("Add XP-Kill: ", false);
		}
		
		if($cfg->get("add-xp-by-chat") === true){
			$form->addToggle("Add XP-Chat: ", true);
		} else {
			$form->addToggle("Add XP-Chat: ", false);
		}
		
		if($cfg->get("kill-with-death-screen") === true){
			$form->addToggle("Death Screen: ", true);
		} else {
			$form->addToggle("Death Screen: ", false);
		}
		
		if($cfg->get("edit-chat-format") === true){
			$form->addToggle("Edit Chat Format: ", true);
		} else {
			$form->addToggle("Edit Chat Format: ", false);
		}
		
		$form->addInput("chat Format: ", "", $cfg->get("chatFormat", "&c[&e{lvl}&c] &r{name} &7> &r{msg}"));
		
		$form->addInput("New Level Message: ", "", $cfg->get("new-level-message", "&eCongratulations, you have reached level {newLevel}"));
		
		$form->addInput("Max Level: ", "", intval($cfg->get("MaxLevel", 100)));
		
		$form->sendToPlayer($player);
		return $form;
	}
}
