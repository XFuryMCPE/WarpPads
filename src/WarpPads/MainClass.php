<?php

/*    ___                 
 *   / __\   _ _ __ _   _ 
 *  / _\| | | | '__| | | |
 * / /  | |_| | |  | |_| |
 * \/    \__,_|_|   \__, |
 *                  |___/
 *
 * No copyright 2016 blahblah
 * Plugin made by fury and is FREE SOFTWARE
 * Do not sell or i will sue you lol
 * but fr tho I will sue ur face
 * DO NOT SELL
 */

namespace WarpPads;

use pocketmine\plugin\PluginBase;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerMoveEvent;

use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;

use pocketmine\math\Vector3;

use WarpPads\commands\SetWP;
use WarpPads\commands\DelWP;

class MainClass extends PluginBase implements Listener{

	public $wpStep1 = [];
	public $wpStep2 = [];

	public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents($this,$this);
		@mkdir($this->getDataFolder());
		if(!file_exists($this->getDataFolder() . "locations.json")){
			$this->locations = new Config($this->getDataFolder() . "locations.json", Config::JSON, [
				"warppads" => [
					"test" => [
						"start" => [
							"x" => 100,
							"y" => 100,
							"z" => 100
						],
						"end" => [
							"x" => 200,
							"y" => 200,
							"z" => 200
						]
					]
				]
			]);
		}
		else{
			$this->locations = new Config($this->getDataFolder() . "locations.json", Config::JSON);
		}
		if(!file_exists($this->getDataFolder() . "config.yml")){
			$this->config = new Config($this->getDataFolder() . "config.yml", Config::YAML, [
				"tp-msg" => "&aTeleported!"
			]);
		}
		else{
			$this->config = new Config($this->getDataFolder() . "config.yml", Config::YAML);
		}

		$commandMap = $this->getServer()->getCommandMap();
		$commandMap->register("setwp",new SetWP($this,"setwp","Create a warp pad!"));
		$commandMap->register("delwp",new DelWP($this,"delwp","Delete a warp pad!"));
	}

	public function onInteract(PlayerInteractEvent $e){
		$p = $e->getPlayer();
		$b = $e->getBlock();
		if(isset($this->wpStep1[$p->getName()])){
			if(!isset($this->wpStep2[$p->getName()])){
				$this->wpStep2[$p->getName()] = [$b->getX(),$b->getY(),$b->getZ()];
				$p->sendMessage(TextFormat::GREEN."Please tap the Ending WarpPad!");
				return;
			}
			$cfg = $this->locations->getAll();
			$cfg["warppads"][$this->wpStep1[$p->getName()]] = [
				"start" => [
					"x" => $this->wpStep2[$p->getName()][0],
					"y" => $this->wpStep2[$p->getName()][1],
					"z" => $this->wpStep2[$p->getName()][2]
				],
				"end" => [
					"x" => floor($b->getX()),
					"y" => floor($b->getY()),
					"z" => floor($b->getZ())
				]
			];
			$this->locations->setAll($cfg);
			$this->locations->save();
			$p->sendMessage(TextFormat::GREEN."WarpPad successfully set!");
			unset($this->wpStep1[$p->getName()]);
			unset($this->wpStep2[$p->getName()]);
		}
	}

	public function onMove(PlayerMoveEvent $e){
		$p = $e->getPlayer();
		$warps = $this->locations->get("warppads");
		foreach($warps as $wps){
			if($wps["start"]["x"] == floor($p->getX()) && $wps["start"]["y"] == floor($p->getY()) - 1 && $wps["start"]["z"] == floor($p->getZ())){
				$p->sendTip($this->translateColors($this->config->get("tp-msg")));
				$endpos = new Vector3($wps["end"]["x"] + 0.5,$wps["end"]["y"] + 1,$wps["end"]["z"] + 0.5);
				$p->teleport($endpos);
			}
		}
	}

	public function translateColors($string){
		$msg = str_replace("&1",TextFormat::DARK_BLUE,$string);
		$msg = str_replace("&2",TextFormat::DARK_GREEN,$msg);
		$msg = str_replace("&3",TextFormat::DARK_AQUA,$msg);
		$msg = str_replace("&4",TextFormat::DARK_RED,$msg);
		$msg = str_replace("&5",TextFormat::DARK_PURPLE,$msg);
		$msg = str_replace("&6",TextFormat::GOLD,$msg);
		$msg = str_replace("&7",TextFormat::GRAY,$msg);
		$msg = str_replace("&8",TextFormat::DARK_GRAY,$msg);
		$msg = str_replace("&9",TextFormat::BLUE,$msg);
		$msg = str_replace("&0",TextFormat::BLACK,$msg);
		$msg = str_replace("&a",TextFormat::GREEN,$msg);
		$msg = str_replace("&b",TextFormat::AQUA,$msg);
		$msg = str_replace("&c",TextFormat::RED,$msg);
		$msg = str_replace("&d",TextFormat::LIGHT_PURPLE,$msg);
		$msg = str_replace("&e",TextFormat::YELLOW,$msg);
		$msg = str_replace("&f",TextFormat::WHITE,$msg);
		$msg = str_replace("&o",TextFormat::ITALIC,$msg);
		$msg = str_replace("&l",TextFormat::BOLD,$msg);
		$msg = str_replace("&r",TextFormat::RESET,$msg);
		return $msg;
	}

}