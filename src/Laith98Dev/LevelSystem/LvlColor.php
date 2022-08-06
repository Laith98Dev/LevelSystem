<?php

namespace Laith98Dev\LevelSystem;

use pocketmine\utils\TextFormat;
use pocketmine\player\Player;

class LvlColor {
  
  public static function getLevel(Player $player): float{
      return Main::getInstance()->getDataManager()->getLevel($player);
  }
  
	/**
     * @param Player $player
     * @return string
     */
	public static function getColorLevel(Player $player) :string{
		$result = "";
		$level = self::getLevel($player);
		if($level >= 0){
			$result = TextFormat::GRAY.$level;
		}
		if($level >= 10){
			$result = TextFormat::GREEN.$level;
		}
		if($level >= 75){
			$result = TextFormat::GOLD.$level;
		}
		if($level >= 100){
			$result = TextFormat::DARK_BLUE.$level;
		}
		if($level >= 200){
			$result = TextFormat::AQUA.$level;
		}
    if($level >= 500){
      $result = TextFormat::LIGHT_PURPLE.$level;
		if($level >= 1000){
			$result = TextFormat::YELLOW.$level;
		}
		return $result;
	}
}
