<?php 

namespace Laith98Dev\LevelSystem\SC;

use Laith98Dev\LevelSystem\Main;
use Ifera\ScoreHud\event\PlayerTagUpdateEvent;
use Ifera\ScoreHud\scoreboard\ScoreTag;
use pocketmine\event\Listener;
use pocketmine\player\Player;

class EventListener implements Listener {
  
  /** @var Main */
	private $plugin;

	public function __construct(Main $plugin){
		$this->plugin = $plugin;
	}
 
  public function onLevelChange(Player $player){ // not a real event]
	        $username = $player->getName();
		$player = $this->plugin->getServer()->getPlayerByPrefix($username);
		if($player instanceof Player && $player->isOnline()){
			(new PlayerTagUpdateEvent($player, new ScoreTag("levelsystem.lvl", (string) $this->plugin->getDataManager()->getLevel())));
		}
  }
}
