<?php

namespace Blubberboy333\PerWorldBan;

use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\entity\EntityLevelChangeEvent;
use pocketmine\event\player\PlayerJoinEvent;

class Main extends PluginBase implements Listener{
    public function onEnable(){
        $this->saveDefaultConfig();
        $this->config = $this->getConfig();
        foreach($this->getServer()->getLevel() as $i){
            $name = $i->getName();
            if(!(file_esists($this->getDataFolder()."Levels/".$name.".yml"))){
                $newFile = new Config($this->getDataFolder()."Levels/".$name.".yml", Config::YAML);
                $this->getLogger()->info(TextFormat::BLUE."Made a file for the level ".$name);
            }
        }
        $this->getLogger()->info(TextFormat::GREEN."Done!");
    }
    
    public function checkBan(Level $level, Player $player){
        $file = new Config($this->getDataFolder()."Levels/".$level.".yml");
        if(isset($file->get($player->getName()))){
            if($file->get($player) == "Banned"){
                return true;
            }else{
                return false;
            }
        }else{
            if($this->getServer()->getPlayer($player) instanceof Player){
                $file->set($player->getName(), "Allowed");
                return false;
            }else{
                return false
            }
        }
    }
    
    public function onPlayerJoinEvent(PlayerJoinEvent $event){
        $player = $event->getPlayer();
        $level = $player->getLevel()->getName();
        if($this->checkBan($level, $player->getName()) == true){
            $world = $this->config->get("World");
            $x = $this->config->get("X");
            $y = $this->config->get("Y");
            $z = $this->config->get("Z");
            $player->teleport(new Position($x, $y, $z, $world));
            $player->sendMessage(TextFormat::RED."You are banned in that world!");
        }
    }
    
    public function onEntityLevelChangeEvent(EntityLevelChangeEvent $event){
        $player = $event->getEntity();
        $level = $player->getLevel()->getName();
        if($player instanceof Player){
            if($this->checkBan($level, $player->getName()) == true){
                $world = $this->config->get("World");
                $x = $this->config->get("X");
                $y = $this->config->get("Y");
                $z = $this->config->get("Z");
                $player->teleport(new Position($x, $y, $z, $world));
                $player->sendMessage(TextFormat::RED."You are banned in that world!");
            }
        }
    }
    
    public function onCommand(CommandSender $sender, Command $command, $label, array $args){
        switch(command->getName()){
            case "worldban":
                if($sender->hasPermission("pwb") || $sender->hasPermission("pwb.cmd") || $sender->hasPermission("pwb.cmd.ban")){
                    if(isset($args[0])){
                        if(isset($args[1])){
                            $player = $this->getServer()->getPlayer($args[0])
                            if($player instanceof Player){
                                $level = $this->getServer()->getLevelByName($args[1]);
                                if($level instanceof Level){
                                    if($this->checkBan($level, $player->getName()) == true){
                                        $sender->sendMessage($player->getName()." is already banned in that world!");
                                        return true;
                                    }else{
                                        $file = new Config($this->getDataFolder()."Levels/".$level.".yml");
                                        $file->set($player->getName(), "Banned");
                                        $sender->sendMessage($player->getName()." has been banned in ".$level->getName());
                                        if($this->checkBan($player->getLevel(), $player->getName() == true){
                                            $world = $this->getConfig()->get("World");
                                            $world = $this->config->get("World");
                                            $x = $this->config->get("X");
                                            $y = $this->config->get("Y");
                                            $z = $this->config->get("Z");
                                            $player->teleport(new Position($x, $y, $z, $world));
                                            $player->sendMessage(TextFormat::RED."You are banned in that world!");
                                        }
                                    }
                                }else{
                                    $sender->sendMessage($args[1]." isn't a level!");
                                    return true;
                                }
                            }else{
                                if($this->checkBan == false){
                                    $sender->sendMessage("That player doesn't exist!");
                                    return true;
                                }else{
                                    $file = new Config($this->getDataFolder()."Levels/".$level.".yml");
                                    $file->set($args[0], "Banned");
                                    $sender->sendMessage
                                }
                            }
                        }
                    }
                }
            case "worldpardon":
                
        }
    }
}
