<?php

namespace cooltimeheal;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;
class Main extends PluginBase implements Listener {
	public function onEnable() {
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		if (!extension_loaded('shmop')) {
			if (strtoupper(substr(PHP_OS, 0, 3 )) === 'WIN') {
				dl('php_shmop.dll');
			} else {
				dl('shmop.so');
			}
		}
	}
	public function onCommand(CommandSender $Sender,Command $command, $label,array $args) {
		if ($command->getName() == '힐') {
			$playername = $Sender->getName();
			$playername = strtolower( $playername );
			$cooltime = 10; //10초
			$playername = base_convert( $playername, 35, 10);
			$shmid = shmop_open($playername, 'c', 0755, 1024);
			if ( shmop_read( $shmid, 0, 11 ) <= time() ) {
				$Sender->sendMessage(TextFormat::RED, "회복되었습니다");
				$Sender->heal($player->getMaxHealth(), new EntityRegainHealthEvent($player, $player->getMaxHealth() - $player->getHealth(), EntityRegainHealthEvent::CAUSE_CUSTOM));
				shmop_write($shmid, time() + $cooltime, 0);
				return true;
			} else {
				$cl = shmop_read( $shmid, 0, 11 ) - time();
				$Sender->sendMessage(TextFormat::RED, '쿨타임'.$cl.'초가 남았습니다');
				return true;
			}
		}
	}
}
?>