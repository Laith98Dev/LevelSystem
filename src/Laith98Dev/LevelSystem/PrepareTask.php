<?php

namespace Laith98Dev\FFA;

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

use pocketmine\scheduler\Task;
use pocketmine\Player;
use pocketmine\block\Block;

class PrepareTask extends Task {
	
	/** @var Main */
	private $plugin;
	
	/** @var Player */
	private $player;
	
	/** @var Block */
	private $block;
	
	/** @var int */
	private $type;
	
	public function __construct(Main $plugin, Player $player, Block $block, int $type){
		$this->plugin = $plugin;
		$this->player = $player;
		$this->block = $block;
		$this->type = $type;
	}
	
	public function onRun(int $tick){
		$player = $this->player;
		$block = $this->block;
		$type = $this->type;
		
		if($type == 1){
			if(($level = $block->getLevel()) !== null){
			if($level->getBlock($block->asVector3())->getId() == $block->getId()){
				if($player instanceof Player){
					var_dump("Event Work\n");
					$cfg = new Config($this->plugin->getDataFolder() . "settings.yml", Config::YAML);
					if($cfg->get("plugin-enable") === true){
						if($cfg->get("add-xp-by-build") === true){
							if(mt_rand(0, 200) < 120 && mt_rand(0, 1) == 1 && mt_rand(0, 1) == 0 && mt_rand(0, 3) == 2){// random
								if($this->plugin->getDataManager()->addXP($player, $this->plugin->getDataManager()->getAddXpCount($player))){
									$player->sendPopup(TF::YELLOW . "+" . $this->plugin->getDataManager()->getAddXpCount($player) . " XP");
								}
							}
						}
					}
				}
			}
		}
		}
		
		if($type == 2){
			if(($level = $block->getLevel()) !== null){
				if($level->getBlock($block->asVector3())->getId() !== $block->getId()){
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
			}
		}
	}
}
