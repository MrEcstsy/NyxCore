<?php

namespace wock\NyxCore;

use DaPigGuy\PiggyFactions\PiggyFactions;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockTypeIds;
use pocketmine\block\BlockTypeInfo;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityExplodeEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerItemConsumeEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerPreLoginEvent;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\StringToEnchantmentParser;
use pocketmine\item\Item;
use pocketmine\item\ItemTypeIds;
use pocketmine\item\StringToItemParser;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use pocketmine\world\particle\FloatingTextParticle;
use pocketmine\world\particle\LavaParticle;
use pocketmine\world\Position;
use skymin\bossbar\BossBarAPI;
use wock\NyxCore\Items\Lootboxes;
use wock\NyxCore\Items\Masks;
use wock\NyxCore\Items\Rewards;
use wock\NyxCore\Utils\Managers\BountyManager;
use wock\NyxCore\Utils\Managers\KDRManager;
use wock\NyxCore\Utils\Managers\SettingsManager;
use wock\NyxCore\Utils\Utils;

class NyxListener implements Listener {

    private SettingsManager $settingsManager;

    public Nyx $plugin;

    public function __construct(SettingsManager $settingsManager) {
        $this->settingsManager = $settingsManager;
        $this->plugin = Nyx::getInstance();
    }

    /**
     * @throws \Exception
     */
    public function onJoin(PlayerJoinEvent $event) {
        $player = $event->getPlayer();
        $nametag = $player->getNameTag();
        $totalonline = count(Server::getInstance()->getOnlinePlayers());
        $playerName = $player->getName();
        $event->setJoinMessage("§r§l§8[§a+§8] §r§7{$nametag} §r§7has entered the domain...");
        $player->getInventory()->addItem(Masks::get(Masks::CHEETAHMASK, 64));

        $message = "§r§7╭━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━╮\n" .
            "§r§f           Welcome to §aETHEREAL§fNETWORK\n" .
            "§r§f        Embark on a Cosmic Journey Through the Stars!\n" .
            "\n" .
            "§r§a       Server Information:\n" .
            "§r§7       • Account: §f" . $player->getName() . "\n" .
            "§r§7       • Connected Players: §f" . count($player->getServer()->getOnlinePlayers()) . "\n" .
            "§r§7       • Webstore: §fstore.etherealnetwork.tk\n" .
            "§r§7       • Discord: §fdiscord.gg/CrV5UFJNxp\n" .
            "\n" .
            "§r§7For additional support, join our community discord!\n" .
            "§r§7╰━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━╯";

        $player->sendMessage($message);
        if (!$this->settingsManager->hasPlayerData($playerName)) {
            $this->settingsManager->createPlayerData($playerName);
        }
        BossBarAPI::getInstance()->sendBossBar($player, "Test BossBar", 0, 1, BossBarAPI::COLOR_WHITE);
        if (!$player->hasPlayedBefore()) {
            $this->starterKitJoin($player);
            $player->getInventory()->addItem(Rewards::get(Rewards::GEARLOOTBOX, 4));
            $count = count($player->getServer()->getOnlinePlayers()) - count(Server::getInstance()->getWhitelisted()->getAll());
            Server::getInstance()->broadcastMessage("§r§f[§e§l#§r§e{$count}§f] §r§7A journey among the §bstars§7 begins as §a{$player->getName()}§7 enters the §jNyx Domain§7.");
        }
    }

    public function starterKitJoin(Player $player)
    {
        $config = Utils::getConfigurations("starterkit");
        $starterItems = $config->getNested("starterItems", []);

        $itemParser = StringToItemParser::getInstance();
        $enchantmentParser = StringToEnchantmentParser::getInstance();
        $inventory = $player->getInventory();

        foreach ($starterItems as $itemData) {
            $itemString = $itemData["item"];
            $name = isset($itemData["name"]) ? $itemData["name"] : null;
            $amount = $itemData["amount"] ?? 1;

            $item = $itemParser->parse($itemString);
            $loreLines = $itemData["lore"] ?? [];

            if ($item instanceof Item) {
                if ($name !== null) {
                    $item->setCustomName(TextFormat::colorize($name));
                }

                $item->setCount($amount);

                $enchantments = $itemData["enchantments"] ?? [];

                foreach ($enchantments as $enchantmentData) {
                    $enchantmentString = $enchantmentData["enchantment"];
                    $level = $enchantmentData["level"] ?? 1;

                    $enchantment = $enchantmentParser->parse($enchantmentString);
                    if ($enchantment instanceof Enchantment) {
                        $item->addEnchantment(new EnchantmentInstance($enchantment, $level));
                    }
                }

                foreach ($loreLines as $loreLine) {
                    $item->getLore()[] = TextFormat::colorize($loreLine);
                }

                $inventory->addItem($item);
            }
        }
    }

    public function onPlayerDeath(PlayerDeathEvent $event) {
        $victim = $event->getPlayer();
        $cause = $victim->getLastDamageCause();
        $bountyManager = new BountyManager(Utils::getConfigurations("bounty"));
        if ($cause instanceof EntityDamageByEntityEvent) {
            $killer = $cause->getDamager();
            if ($killer instanceof Player) {
                $victimName = $victim->getName();
                $victimBounty = $bountyManager->getBounty($victim->getName());
                if ($victimBounty > 0) {
                    $claimedAmount = $bountyManager->claimBounty($killer, $victimName);
                    $killer->getServer()->broadcastMessage("§r§a{$killer->getName()} §7claimed a bounty of §a§l$" . number_format($claimedAmount) . " §r§7from §a$victimName!");
                }
            }
        }
    }

    public function onKDRChange(PlayerDeathEvent $event) {
        $player = $event->getPlayer();
        $cause = $player->getLastDamageCause();

        if ($cause instanceof EntityDamageByEntityEvent) {
            $damager = $cause->getDamager();
            if ($damager instanceof Player) {
                $this->addKill($damager);
            }
        }

        $this->addDeath($player);
    }

    private function addKill(Player $player)
    {
        $kdrManager = new KDRManager(Utils::getConfigurations("kdr")->getPath());
        $kdr = $kdrManager->getKdr($player->getName());
        $kdr["kills"]++;
        $kdrManager->updateKdr($player->getName(), 1, 0, 1);
        $kdrManager = new KDRManager(Utils::getConfigurations("kdr")->getPath());
        $playerKills = $kdrManager->getKills();
        arsort($playerKills);
        $leaderboard = array_keys($playerKills); // Get the leaderboard positions
        $currentLeader = $leaderboard[0]; // Get the Player at the first position

        if ($player->getName() === $currentLeader && $player->getName() !== $this->previousLeader) {
            $message = "§r§l§3Leaderboard §r§8| §r§3" . $player->getName() . "§r§f is now in first place with §l§3" . number_format($playerKills[$currentLeader]) . "§r§f kills!";
            Server::getInstance()->broadcastMessage($message);
        }
    }

    private function addDeath(Player $player) {
        $kdrManager = new KDRManager(Utils::getConfigurations("kdr")->getPath());
        $kdr = $kdrManager->getKdr($player->getName());
        $kdr["deaths"]++;
        $kdrManager->updateKdr($player->getName(), 0, 1, 0);
        $playerName = $player->getName();

        if ($this->previousLeader === $playerName) {
            $this->previousLeader = null;
        }
    }
}
