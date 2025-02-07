<?php

namespace Aryanxxpro\BowParticle;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\player\Player;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\utils\Config;
use pocketmine\event\entity\ProjectileLaunchEvent;
use pocketmine\entity\projectile\Arrow;
use pocketmine\scheduler\ClosureTask;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\world\particle\FlameParticle;
use pocketmine\world\particle\WaterParticle;
use pocketmine\world\particle\HeartParticle;
use pocketmine\world\particle\SmokeParticle;
use pocketmine\world\particle\PortalParticle;
use pocketmine\world\particle\LavaDripParticle;
use pocketmine\world\particle\WaterDripParticle;
use pocketmine\world\particle\RedstoneParticle;
use pocketmine\world\particle\SnowballPoofParticle;
use pocketmine\world\particle\AngryVillagerParticle;
use pocketmine\world\particle\HappyVillagerParticle;
use pocketmine\world\particle\EnchantmentTableParticle;
use jojoe77777\FormAPI\SimpleForm;

class Main extends PluginBase implements Listener {

    public function onEnable(): void {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->config = $this->getConfig();
        $this->saveDefaultConfig();
        $this->saveResource("players/");
        @mkdir($this->getDataFolder() . "players", 0777, true);
    }

    public function onPlayerJoin(PlayerJoinEvent $event): void {
        $name = $event->getPlayer()->getName();
        $file = $this->getDataFolder() . "players/$name.yml";

        if(!file_exists($file)) {
            $config = new Config($file, Config::YAML);
            $config->set("particle", false);
            $config->save();
        }
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool {
        if ($sender instanceof Player) {
		    switch($command->getName()){
                case "bowparticle":
                     $this->menu($sender);
                   return true;
            }
            return true;
        } else {
            $sender->sendMessage("§cUse this command in-game!");
            return false;
        }
    }

    public function menu(Player $player): void {

		$form = new SimpleForm(function(Player $player, int $data = null){
			if($data === null)
			{
				return;
			}
            $prefix = $this->config->get("prefix");
            $name = $player->getName();
            $config = new Config($this->getDataFolder() . "players/$name.yml", Config::YAML);

			switch($data)
			{
				case 0:
                    $player->sendMessage($prefix . "§aFlame bow particle enabled!");
					$config->set("particle", "flame");
					$config->save();
				break;
				case 1:
                    $player->sendMessage($prefix . "§aWater bow particle enabled!");
					$config->set("particle", "water");
                    $config->save();			
				break;
				case 2:
                    $player->sendMessage($prefix . "§aHeart bow particle enabled!");
					$config->set("particle", "heart");
                    $config->save();
				break;
                case 3:
                    $player->sendMessage($prefix . "§aSmoke bow particle enabled!");
					$config->set("particle", "smoke");
                    $config->save();
				break;
                case 4:
                    $player->sendMessage($prefix . "§aPortal bow particle enabled!");
					$config->set("particle", "portal");
                    $config->save();
				break;
                case 5:
                    $player->sendMessage($prefix . "§aLavaDrip bow particle enabled!");
					$config->set("particle", "lavadrip");
                    $config->save();
				break;
                case 6:
                    $player->sendMessage($prefix . "§aWaterDrip bow particle enabled!");
					$config->set("particle", "waterdrip");
                    $config->save();
				break;
                case 7:
                    $player->sendMessage($prefix . "§aRedstone bow particle enabled!");
					$config->set("particle", "redstone");
                    $config->save();
				break;
                case 8:
                    $player->sendMessage($prefix . "§aSnowballPoof bow particle enabled!");
					$config->set("particle", "snowballpoof");
                    $config->save();
				break;
                case 9:
                    $player->sendMessage($prefix . "§aAngryVillager bow particle enabled!");
					$config->set("particle", "angryvillager");
                    $config->save();
				break;
                case 10:
                    $player->sendMessage($prefix . "§aHappyVillager bow particle enabled!");
					$config->set("particle", "happyvillager");
                    $config->save();
				break;
                case 11:
                    $player->sendMessage($prefix . "§aEnchantmentTable bow particle enabled!");
					$config->set("particle", "enchantmenttable");
                    $config->save();
				break;
                case 12:
                    $player->sendMessage($prefix . "§cBow particle disabled!");
					$config->set("particle", "false");
                    $config->save();
				break;
			}
		});
		$form->setTitle("Bow Particle");
		$form->setContent("Select a particle for your bow shoots using the buttons below:");
		$form->addButton("Flame",0, "textures/ui/realms_green_check");
		$form->addButton("Water",0, "textures/ui/realms_green_check");
		$form->addButton("Heart", 0, "textures/ui/realms_green_check");
		$form->addButton("Smoke",0, "textures/ui/realms_green_check");
		$form->addButton("Portal", 0, "textures/ui/realms_green_check");
		$form->addButton("LavaDrip",0, "textures/ui/realms_green_check");
		$form->addButton("WaterDrip", 0, "textures/ui/realms_green_check");
		$form->addButton("Redstone",0, "textures/ui/realms_green_check");
		$form->addButton("SnowballPoof",0, "textures/ui/realms_green_check");
		$form->addButton("AngryVillager", 0, "textures/ui/realms_green_check");
		$form->addButton("HappyVillager",0, "textures/ui/realms_green_check");
		$form->addButton("EnchantmentTable", 0, "textures/ui/realms_green_check");
        $form->addButton("Remove", 0, "textures/ui/cancel");
		$form->sendToPlayer($player);
    }

    public function onArrowShoot(ProjectileLaunchEvent $event): void {
        $projectile = $event->getEntity();

        if ($projectile instanceof Arrow) {
            $player = $projectile->getOwningEntity();

            if ($player instanceof Player) {
                $name = $player->getName();
                $config = new Config($this->getDataFolder() . "players/$name.yml", Config::YAML);
                $ticks = $this->config->get("ticks");

                if($config->get("particle") === "flame") {
                    $this->getScheduler()->scheduleRepeatingTask(new ClosureTask(function () use ($projectile) {
                        if (!$projectile->isAlive()) return;

                        $motion = $projectile->getMotion();
                        $speed = $motion->lengthSquared();

                        if ($speed > 0) {     
                            $position = $projectile->getPosition();
                            $world = $projectile->getWorld();
                            $world->addParticle($position, new FlameParticle());
                            $projectile->setCritical(false);
                        }
                    }), $ticks);
                }

                if($config->get("particle") === "water") {
                    $this->getScheduler()->scheduleRepeatingTask(new ClosureTask(function () use ($projectile) {
                        if (!$projectile->isAlive()) return;
            
                        $motion = $projectile->getMotion();
                        $speed = $motion->lengthSquared();
            
                        if ($speed > 0) {     
                            $position = $projectile->getPosition();
                            $world = $projectile->getWorld();
                            $world->addParticle($position, new WaterParticle());
                            $projectile->setCritical(false);
                        }
                    }), $ticks);
                }

                if($config->get("particle") === "heart") {
                    $this->getScheduler()->scheduleRepeatingTask(new ClosureTask(function () use ($projectile) {
                        if (!$projectile->isAlive()) return;
            
                        $motion = $projectile->getMotion();
                        $speed = $motion->lengthSquared();
            
                        if ($speed > 0) {     
                            $position = $projectile->getPosition();
                            $world = $projectile->getWorld();
                            $world->addParticle($position, new HeartParticle());
                            $projectile->setCritical(false);
                        }
                    }), $ticks);
                }

                if($config->get("particle") === "smoke") {
                    $this->getScheduler()->scheduleRepeatingTask(new ClosureTask(function () use ($projectile) {
                        if (!$projectile->isAlive()) return;
            
                        $motion = $projectile->getMotion();
                        $speed = $motion->lengthSquared();
            
                        if ($speed > 0) {     
                            $position = $projectile->getPosition();
                            $world = $projectile->getWorld();
                            $world->addParticle($position, new SmokeParticle());
                            $projectile->setCritical(false);
                        }
                    }), $ticks);
                }

                if($config->get("particle") === "portal") {
                    $this->getScheduler()->scheduleRepeatingTask(new ClosureTask(function () use ($projectile) {
                        if (!$projectile->isAlive()) return;
            
                        $motion = $projectile->getMotion();
                        $speed = $motion->lengthSquared();
            
                        if ($speed > 0) {     
                            $position = $projectile->getPosition();
                            $world = $projectile->getWorld();
                            $world->addParticle($position, new PortalParticle());
                            $projectile->setCritical(false);
                        }
                    }), $ticks);
                }

                if($config->get("particle") === "lavadrip") {
                    $this->getScheduler()->scheduleRepeatingTask(new ClosureTask(function () use ($projectile) {
                        if (!$projectile->isAlive()) return;
            
                        $motion = $projectile->getMotion();
                        $speed = $motion->lengthSquared();
            
                        if ($speed > 0) {     
                            $position = $projectile->getPosition();
                            $world = $projectile->getWorld();
                            $world->addParticle($position, new LavaDripParticle());
                            $projectile->setCritical(false);
                        }
                    }), $ticks);
                }

                if($config->get("particle") === "waterdrip") {
                    $this->getScheduler()->scheduleRepeatingTask(new ClosureTask(function () use ($projectile) {
                        if (!$projectile->isAlive()) return;
            
                        $motion = $projectile->getMotion();
                        $speed = $motion->lengthSquared();
            
                        if ($speed > 0) {     
                            $position = $projectile->getPosition();
                            $world = $projectile->getWorld();
                            $world->addParticle($position, new WaterDripParticle());
                            $projectile->setCritical(false);
                        }
                    }), $ticks);
                }

                if($config->get("particle") === "redstone") {
                    $this->getScheduler()->scheduleRepeatingTask(new ClosureTask(function () use ($projectile) {
                        if (!$projectile->isAlive()) return;
            
                        $motion = $projectile->getMotion();
                        $speed = $motion->lengthSquared();
            
                        if ($speed > 0) {     
                            $position = $projectile->getPosition();
                            $world = $projectile->getWorld();
                            $world->addParticle($position, new RedstoneParticle());
                            $projectile->setCritical(false);
                        }
                    }), $ticks);
                }

                if($config->get("particle") === "snowballpoof") {
                    $this->getScheduler()->scheduleRepeatingTask(new ClosureTask(function () use ($projectile) {
                        if (!$projectile->isAlive()) return;
            
                        $motion = $projectile->getMotion();
                        $speed = $motion->lengthSquared();
            
                        if ($speed > 0) {     
                            $position = $projectile->getPosition();
                            $world = $projectile->getWorld();
                            $world->addParticle($position, new SnowballPoofParticle());
                            $projectile->setCritical(false);
                        }
                    }), $ticks);
                }

                if($config->get("particle") === "angryvillager") {
                    $this->getScheduler()->scheduleRepeatingTask(new ClosureTask(function () use ($projectile) {
                        if (!$projectile->isAlive()) return;
            
                        $motion = $projectile->getMotion();
                        $speed = $motion->lengthSquared();
            
                        if ($speed > 0) {     
                            $position = $projectile->getPosition();
                            $world = $projectile->getWorld();
                            $world->addParticle($position, new AngryVillagerParticle());
                            $projectile->setCritical(false);
                        }
                    }), $ticks);
                }

                if($config->get("particle") === "happyvillager") {
                    $this->getScheduler()->scheduleRepeatingTask(new ClosureTask(function () use ($projectile) {
                        if (!$projectile->isAlive()) return;
            
                        $motion = $projectile->getMotion();
                        $speed = $motion->lengthSquared();
            
                        if ($speed > 0) {     
                            $position = $projectile->getPosition();
                            $world = $projectile->getWorld();
                            $world->addParticle($position, new HappyVillagerParticle());
                            $projectile->setCritical(false);
                        }
                    }), $ticks);
                }

                if($config->get("particle") === "enchantmenttable") {
                    $this->getScheduler()->scheduleRepeatingTask(new ClosureTask(function () use ($projectile) {
                        if (!$projectile->isAlive()) return;
            
                        $motion = $projectile->getMotion();
                        $speed = $motion->lengthSquared();
            
                        if ($speed > 0) {     
                            $position = $projectile->getPosition();
                            $world = $projectile->getWorld();
                            $world->addParticle($position, new EnchantmentTableParticle());
                            $projectile->setCritical(false);
                        }
                    }), $ticks);
                }
            }
        }
    }
}
