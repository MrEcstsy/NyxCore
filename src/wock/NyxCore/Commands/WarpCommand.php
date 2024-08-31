<?php

namespace wock\NyxCore\Commands;

use jojoe77777\FormAPI\SimpleForm;
use muqsit\customsizedinvmenu\CustomSizedInvMenu;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\DeterministicInvMenuTransaction;
use pocketmine\block\utils\DyeColor;
use pocketmine\block\VanillaBlocks;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\world\Position;
use wock\NyxCore\Utils\Managers\SettingsManager;

class WarpCommand extends Command {

    public SettingsManager $settingsManager;

    public function __construct(SettingsManager $settingsManager) {
        parent::__construct("warps");
        $this->setPermission("Nyx.warps");
        $this->settingsManager = $settingsManager;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool
    {
        if (!$sender instanceof Player) {
            $sender->sendMessage("You must run this command in-game");
            return false;
        }
        if ($sender->hasPermission("Nyx.warp.Nyx")) {
            $text = "§r§l§aUNLOCKED\n§r§7Click to teleport to this warp";
        } else {
            $text = "§r§l§cLOCKED\n§r§7You do not have access to this warp";
        }
        if ($this->settingsManager->isChestGUISettingEnabled($sender->getName())) {
            $warpgui = CustomSizedInvMenu::create(9);
            $warpgui->setName("§r§l§8Nyx Warps");
            $canEnter = "§r§7You can re-enter spawn in this world.";
            //$endCount = count(Server::getInstance()->getWorldManager()->getWorldByName("End")->getPlayers());
            $warpinv = $warpgui->getInventory();
            $warpinv->setItem(0, VanillaBlocks::SAND()->asItem()->setCustomName("§r§l§6Desert Warp")->setLore(["§r§eAn expansive flat desert biome,", "§r§eperfect for intense PvP", "", "§r§l§6Coords", "§r§7 ➥ §eX: -100, Z: 100", "", "§r§l§7(§6!§7) §r§7Click to teleport to this warp."]));
            $warpinv->setItem(2, VanillaBlocks::NETHERRACK()->asItem()->setCustomName("§r§l§cNether Warp")->setLore([$canEnter, "", "§r§l§cNether Koth §r§7(X: -100, Z: 100)", "§r§l§c* §r§7Capture Time: §c5 minutes", "§r§l§c* §r§7Rewards: §c2x KoTH Lootbag(s)", "", "§r§l§4Coords", "§r§7 ➥ §cX: 0, Z: 0", "", "§r§l§7(§c!§7) §r§7Click to teleport to this warp"]));
            $warpinv->setItem(3, VanillaBlocks::END_STONE()->asItem()->setCustomName("§r§l§5End Warp")->setLore([$canEnter, "", "§r§l§5* §r§c§k: §r§cONLY EXIT FROM THIS WARP §k:§r", "§r§l§5* §r§c§k: §r§cIS THROUGH THE PORTAL §k:§r", "", "§r§l§5Portal Coords", "§r§7 ➥ §dX: -271, Z: -176", "", "§r§5§l* §r§dGrinding Rewards", "§r§l§5* §r§dEnder Dragon Event", "", "§r§l§5Players", "§r§7 ➥ §f" . "0", "", "§r§l§7(§c!§7) §r§7Click to teleport to this warp"]));
            $warpinv->setItem(4, VanillaBlocks::BEACON()->asItem()->setCustomName("§r§l§aSpawn Teleporter")->setLore(["§r§7Click to teleport to spawn"]));
            $warpinv->setItem(6, VanillaBlocks::TNT()->asItem()->setCustomName("§r§l§cPvP Arena")->setLore(["§r§7Click to teleport to the §c§lPvP Arena§r"]));
            $warpinv->setItem(7, VanillaItems::NETHER_STAR()->setCustomName("§r§l§2/Warp §a§k:§f: §r§l§aF§2a§an§2t§aa§2s§ay §f§k:§a:§r")->setLore(["§r§7A warp that will unlock", "§r§7your secret powers.", "", "§r§l§aBuffs", "§r§f ➥ §2No Coinflip Tax", "§r§f ➥ §2No Jackpot tax", "§r§f ➥ §2Save Slot Bot Ticket §r§7(1%)", "", $text]));
            $warpinv->setItem(8, VanillaItems::DIAMOND_SWORD()->setCustomName("§r§l§3Fantasy Outposts"));

            $skippedSlots = [0, 2, 3, 4, 6, 7, 8];

            for ($slot = 0; $slot < $warpinv->getSize(); $slot++) {
                if (in_array($slot, $skippedSlots)) {
                    continue;
                }

                $warpinv->setItem($slot, VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::BLACK())->asItem()->setCustomName(" "));
            }

            $warpgui->setListener(InvMenu::readonly(function (DeterministicInvMenuTransaction $event): void {

            }));

            $warpgui->send($sender);
        } else {
            // Warp Form
            $this->warpForm($sender);
        }

        return true;
    }

    public function warpForm(Player $player): void {
        if ($player->hasPermission("Nyx.warp.Nyx")) {
            $text = "§r§l§aUNLOCKED";
        } else {
            $text = "§r§l§cLOCKED";
        }
        $warpform = new SimpleForm(function (Player $player, ?int $data) {
            if ($data === null) {
                // The player closed the form
                return;
            }

            switch ($data) {
                case 0:
                    // Option 1 selected
                    $player->sendMessage("You selected Desert");
                    break;
                case 1:
                    // Option 2 selected
                    $player->sendMessage("You selected Nether.");
                    break;
                case 2:
                    // Option 3 selected
                    $this->endForm($player);
                    break;
                case 3:
                    $player->sendMessage("You selected Spawn.");
                    break;
                case 4:
                    $player->sendMessage("You selected PvP Arena.");
                    break;
                case 5:
                    $player->sendMessage("You selected Fantasy");
                    break;
                case 6:
                    $player->sendMessage("You selected Fantasy Outposts");
                    break;
            }
        });

        $warpform->setTitle("§r§l§8Nyx Warps");
        $warpform->setContent("Select a warp to teleport:");
        $warpform->addButton("§r§l§6Desert");
        $warpform->addButton("§r§l§cNether");
        $warpform->addButton("§r§l§5End");
        $warpform->addButton("§r§l§aSpawn Teleporter");
        $warpform->addButton("§r§l§cPvP Arena");
        $warpform->addButton("§r§l§a§k:§f:§r§l§a F§2a§an§2t§as§2y §r§l§f§k:§a:§r \n$text");
        $warpform->addButton("§r§l§3Fantasy Outposts");

        $player->sendForm($warpform);
    }

    public function endForm(Player $player): void {
        $secondForm = new SimpleForm(function (Player $player, ?int $data) {
            if ($data === null) {
                // The player closed the form
                return;
            }

            switch ($data) {
                case 0:
                    $endworld = Server::getInstance()->getWorldManager()->getWorldByName("End");
                    $player->teleport(new Position("256", "65", "256", $endworld));
                    break;
                case 1:
                    // Second button selected
                    $player->sendMessage("You selected the second option.");
                    break;
            }
        });
        $endPlayers = count(Server::getInstance()->getWorldManager()->getWorldByName("End")->getPlayers());

        $secondForm->setTitle("§r§l§8End /warp");
        $secondForm->setContent("§r§7You can re-enter spawn in this world.\n\n§r§5* §4: §r§cOnly exit from this warp §4 :§r\n§r§5*§4 : §r§cis through the portal §4§l:§r\n\n§r§l§5Portal Coords\n§r§7 ➥ §dX: -231, Z: -176\n\n§r§5* §dGrinding Rewards\n§r§5* §dEnder Dragon Event\n\n§r§5§lPlayers\n§r§7 ➥ §f" . $endPlayers);
        $secondForm->addButton("§r§l§8Teleport");
        $secondForm->addButton("§r§l§8Cancel");

        $player->sendForm($secondForm);
    }
}
