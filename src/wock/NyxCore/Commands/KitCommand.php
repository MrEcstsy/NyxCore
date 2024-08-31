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

class KitCommand extends Command {

    public SettingsManager $settingsManager;

    public function __construct(SettingsManager $settingsManager) {
        parent::__construct("kit", "Open the kit menu", "/kit", ["kits"]);
        $this->setPermission("Nyx.kit");
        $this->settingsManager = $settingsManager;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool
    {
        if (!$sender instanceof Player) {
            $sender->sendMessage("You must run this command in-game");
            return false;
        }
        if ($sender->hasPermission("NyxCore.kit.nitro")) {
            $text = "§r§l§aUNLOCKED";
        } else {
            $text = "§r§l§cLOCKED";
        }
        if ($this->settingsManager->isChestGUISettingEnabled($sender->getName())) {
            $warpgui = CustomSizedInvMenu::create(27);
            $warpgui->setName("§r§l§8Fantasy Kit Menu");
            $warpinv = $warpgui->getInventory();
            $store = "store.NyxCorepe.org";
            $warpinv->setItem(10, VanillaItems::CLOCK()->setCustomName("§r§l§eRank Kits")->setLore(["§r§7These kits are unlocked with ranks.", "", "§r§e" . $store]));
            $warpinv->setItem(12, VanillaItems::BLAZE_POWDER()->setCustomName("§r§l§cWarrior Kits")->setLore(["§r§7These kits are unlocked in-game.", "", "§r§c" . $store]));
            $warpinv->setItem(14, VanillaItems::BOOK()->setCustomName("§r§l§aGlobal Kits")->setLore(["§r§7These kits are unlocked globally.", "", "§r§a" . $store]));
            $warpinv->setItem(16, VanillaBlocks::BEACON()->asItem()->setCustomName("§r§l§dNitro Kit")->setLore(["§r§7This kit is unlocked by boosting the discord.", "", $text, "§r§l§7REQUIRES: §r§dNitro boosting §7our discord & then §a/sync"]));

            $skippedSlots = [10, 12, 14, 16];

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
            $this->kitForm($sender);
        }

        return true;
    }

    public function kitForm(Player $player): void {
        if ($player->hasPermission("NyxCore.kit.nitro")) {
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
                    $player->sendMessage("You selected rank kits");
                    break;
                case 1:
                    // Option 2 selected
                    $player->sendMessage("You selected warrior kits.");
                    break;
                case 2:
                    $player->sendMessage("You selected global kits.");
                    break;
                case 3:
                    $player->sendMessage("You selected nitro kit.");
                    break;
            }
        });

        $warpform->setTitle("§r§l§8Fantasy Kit Menu");
        $warpform->setContent("Select a kit menu to continue:");
        $warpform->addButton("§r§l§eRank Kits");
        $warpform->addButton("§r§l§cWarrior Kits");
        $warpform->addButton("§r§l§aGlobal Kits");
        $warpform->addButton("§r§l§dNitro Kit\n$text");

        $player->sendForm($warpform);
    }
}
