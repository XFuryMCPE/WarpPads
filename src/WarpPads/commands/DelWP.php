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

namespace WarpPads\commands;

use WarpPads\MainClass;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;

use pocketmine\utils\TextFormat;

class DelWP extends Command implements PluginIdentifiableCommand{

	public function __construct(MainClass $plugin,$name,$description){
		$this->plugin = $plugin;
		parent::__construct($name,$description);
		$this->setPermission("warppads.op");
	}

	public function execute(CommandSender $sender, $label, array $args){
		if(!$sender->hasPermission("warppads.op")){
			$sender->sendMessage(TextFormat::RED."You do not have permission to use this command!");
			return;
		}
		if(count($args) != 1){
			$sender->sendMessage(TextFormat::RED."Usage: /delwp <name>");
			return;
		}
		$loc = $this->plugin->locations->get("warppads");
		$selection = $loc[strtolower($args[0])];
		if($selection == null){
			$sender->sendMessage(TextFormat::RED."WarpPad doesn't exist!");
			return;
		}
		$ga = $this->plugin->locations->getAll();
		unset($ga["warppads"][strtolower($args[0])]);
		$this->plugin->locations->setAll($ga);
		$this->plugin->locations->save();
		$sender->sendMessage(TextFormat::RED."WarpPad deleted!");
	}

	public function getPlugin(){
		return $this->plugin;
	}
}