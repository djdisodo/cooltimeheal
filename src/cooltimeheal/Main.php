<?php

namespace cooltimeheal;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;
use pocketmine\event\entity\EntityRegainHealthEvent;
use pocketmine\Player;
class Main extends PluginBase implements Listener {
	public function onEnable() {
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		if (!extension_loaded('shmop')) { //shmop익스텐션이 없으면 로드
			if (strtoupper(substr(PHP_OS, 0, 3 )) === 'WIN') {
				dl('php_shmop.dll');
			} else {
				dl('shmop.so');
			}
		}
	}
	public function onCommand(CommandSender $Sender,Command $command, $label,array $args) {
		if(!($Sender instanceof Player)){ //실행자가 콘솔인지 확인
			$Sender->sendMessage("이 명령어는 인게임에서만 실행 가능합니다");
			return false;
		}
		if ($command->getName() == '힐') {
			$playername = 'cth'.$Sender->getName();
			$playername = strtolower( $playername );
			$cooltime = 10; //10초
			$playername = base_convert( $playername, 35, 10); //10진으로 변환
			$shmid = shmop_open((int)$playername, 'c', 0755, 1024);
			if ( shmop_read( $shmid, 0, 11 ) <= time() ) {
				$Sender->sendMessage(TextFormat::RED . "회복되었습니다");
				$Sender->heal($Sender->getMaxHealth(), new EntityRegainHealthEvent($Sender, $Sender->getMaxHealth() - $Sender->getHealth(), EntityRegainHealthEvent::CAUSE_CUSTOM));
				shmop_write($shmid, time() + $cooltime, 0);
				return true;
			} else {
				$cl = shmop_read( $shmid, 0, 11 ) - time();
				$Sender->sendMessage(TextFormat::RED . '쿨타임'.$cl.'초가 남았습니다');
				return true;
			}
		}
	}
}
?>
