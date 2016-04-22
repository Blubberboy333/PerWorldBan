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
        if($file->get($player->getName()) !== null){
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
                return false;
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
            if($this->checkBan($this->getServer()->getLevelByName($this->getConfig()->get("World")), $player) == false){
                $player->teleport(new Position($x, $y, $z, $world));
                $player->sendMessage(TextFormat::RED."You are banned in that world!");
            }else{
                $player->kick("There has been an error.");
            }
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
                if($this->checkBan($this->getServer()->getLevelByName($this->getConfig()->get("World")), $player) == false){
                    $player->teleport(new Position($x, $y, $z, $world));
                    $player->sendMessage(TextFormat::RED."You are banned in that world!");
                }else{
                    $player->kick("There has been an error.");
                }
            }
        }
    }
    
    public function onCommand(CommandSender $sender, Command $command, $label, array $args){
        switch($command->getName()){
            case "worldban":
                if($sender->hasPermission("pwb") || $sender->hasPermission("pwb.cmd") || $sender->hasPermission("pwb.cmd.ban")){
                    if(isset($args[0])){
                        if(isset($args[1])){
                            $player = $this->getServer()->getPlayer($args[0]);
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
                                        $this->getLogger()->info("[".$sender->getName()." banned ".$player->getName()." in ".$level->getName());
                                        if($this->checkBan($player->getLevel(), $player->getName() == true)){
                                            $world = $this->getConfig()->get("World");
                                            $world = $this->config->get("World");
                                            $x = $this->config->get("X");
                                            $y = $this->config->get("Y");
                                            $z = $this->config->get("Z");
                                            if($this->getServer()->getLevelByName($this->getConfig()->get("World")), $player) == false){)
                                                $player->teleport(new Position($x, $y, $z, $world));
                                                $player->sendMessage(TextFormat::RED."You are banned in that world!");
                                            }else{
                                                $sender->sendMessage(TextFormat::RED."You can't ban players in that world!");
                                                return true;
                                            }
                                        }
                                    }
                                }else{
                                    $sender->sendMessage(TextFormat::YELLOW.$args[1]." isn't a level!");
                                    return true;
                                }
                            }else{
                                if($this->checkBan == false){
                                    $sender->sendMessage(TextFormat::RED."That player doesn't exist!");
                                    return true;
                                }else{
                                    $file = new Config($this->getDataFolder()."Levels/".$level.".yml");
                                    $file->set($args[0], "Banned");
                                    $sender->sendMessage($args[0]." has been banned in ".$level);
                                    $level = $this->getServer()->getLevelByName($args[0]);
                                    $this->getLogger()->info($sender->getName()." banned ".$args[0]."in ".$level);
                                }
                            }
                        }else{
                            $sender->sendMessage(TextFormat::YELLOW."You need to specify a Level!");
                            return false;
                        }
                    }else{
                        $sender->sendMessage(TextFormat::YELLOW."You need to specify a player!");
                        return false;
                    }
                }else{
                    $sender->sendMessage(TextFormat::RED."You don't have permission to use that command!");
                    return true;
                }
            case "worldpardon":
                if($sender->hasPermission("pwb") || $sender->hasPermission("pwb.cmd") || $sender->hasPermission("pwb.cmd.pardon")){
                    if(isset($args[0])){
                        if($isset($args[1])){
                            $player = $this->getServer()->getPlayer($args[0]);
                            if($player instanceof Player){
                                $level = $this->getServer()->getLevelByName($args[1]);
                                if($level instanceof Level){
                                    if($this->checkBan($level->getName(), $player->getName()) == true){
                                        $file = new Config($this->getDataFolder()."Levels/".$level->getName().".yml", Config::YAML);
                                        $file->set($player->getName(), "Allowed");
                                        $sender->sendMessage($player->getName()." has been pardoned in ".$level->getName());
                                        $this->getLogger()->info("[".$sender->getName()." pardoned ".$player->getName()." in ".$level->getName()."]");
                                        return true;
                                    }else{
                                        $sender->sendMessage(TextFormat::YELLOW."That player is not banned in ".$level->getName());
                                        return true;
                                    }
                                }else{
                                    $sender->sendMessage(TextFormat::YELLOW."There is not level by that name!");
                                    return true;
                                }
                            }else{
                                if($this->checkBan($args[0], $level->getName()) == true){
                                    $file = new Config($this->getDataFolder()."Levels/".$level->getName().".yml", Config::YAML);
                                    $file->set($args[0], "Allowed");
                                    $sender->sendMessage($args[0]." has been pardoned in ".$level->getName());
                                    $this->getLogger()->info("[".$sender->getName()." pardoned ".$args[0]." in ".$level->getName()."]");
                                    return true;
                                }else{
                                    $sender->sendMessage(TextFormat::RED."That player doesn't exist!");
                                    return true;
                                }
                            }
                        }else{
                            $sender->sendMessage(TextFormat::YELLOW."You need to specify a level!");
                            return false;
                        }
                    }else{
                        $sender->sendMessage(TextFormat::YELLOW."You need to specify a player!");
                        return true;
                    }
                }else{
                    $sender->sendMessage(TextFormat::RED."You don't have permission to use that command!");
                    return true;
                }
        }
    }
}
