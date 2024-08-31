<?php

namespace wock\NyxCore\Commands;

use jojoe77777\FormAPI\SimpleForm;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\DeterministicInvMenuTransaction;
use muqsit\invmenu\type\InvMenuTypeIds;
use pocketmine\block\utils\DyeColor;
use pocketmine\block\VanillaBlocks;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\item\ItemTypeIds;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use wock\NyxCore\Items\Rewards;
use wock\NyxCore\Utils\Managers\SettingsManager;
use wock\NyxCore\Utils\Utils;

class XPShopCommand extends Command
{

    public SettingsManager $settingsManager;

    public function __construct(SettingsManager $settingsManager) {
        parent::__construct("xpshop", "Open the xpshop menu", "/xpshop");
        $this->setPermission("Nyx.xpshop");
        $this->settingsManager = $settingsManager;
    }

    /**
     * @inheritDoc
     * @throws \Exception
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if (!$sender instanceof Player) {
            $sender->sendMessage(TextFormat::DARK_RED . "You must run this command in-game");
            return false;
        }

        if ($this->settingsManager->isChestGUISettingEnabled($sender->getName())) {
                $menu = InvMenu::create(InvMenuTypeIds::TYPE_DOUBLE_CHEST);

                $menu->setName("§r§8XP Shop");

                $inventory = $menu->getInventory();
                $glassPane = VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::BLACK())->asItem();
                for ($i = 0; $i < 54; $i++) {
                    if ($i % 9 === 0 || $i % 9 === 8 || $i < 9 || $i >= 45) {
                        $inventory->setItem($i, $glassPane);
                    }
                }
                $inventory->setItem(49, VanillaItems::EXPERIENCE_BOTTLE()->setCustomName("§r§l§9Nyx XP Shop")->setLore([
                    "§r§7This shop contains various items that will help",
                    "§r§7you set up on the Nyx Domain.",
                    "",
                    "§r§9Your EXP: §f" . number_format($sender->getXpManager()->getCurrentTotalXp())
                ]));

                /*          Loot Table          */
                $inventory->setItem(10, VanillaItems::NETHER_QUARTZ()->setCustomName("§r§l§f➥ §1Fragment §fGenerator §r§7(Right Click)")->setLore([
                    "",
                    "§r§7Right-Click (in your hand) to receive",
                    "§r§7one of the fragments listed below.",
                    "",
                    "§r§f§lRandom Loot (§r§71 Item(s)§l§f)",
                    "§r§f§l * 1x §bEnchantment Fragment [§r§7Depth Strider III§l§b]",
                    "§r§f§l * 1x §cEnchantment Fragment [§r§7Thorns III§c§l]",
                    "§r§f§l * 1x §bEnchantment Fragment [§r§dUnbreaking V§b§l]",
                    "§r§f§l * 1x §bEnchantment Fragment [§r§dLooting V§l§b]",
                    "",
                    "§r§l§9* Cost: §r§f5,000 §l§9EXP"
                ]));

            $menu->setListener(InvMenu::readonly(function (DeterministicInvMenuTransaction $transaction) : void {
                $player = $transaction->getPlayer();
                $itemClicked = $transaction->getItemClicked();
                $slot = $transaction->getAction()->getSlot();

                if ($itemClicked->getTypeId() === ItemTypeIds::NETHER_QUARTZ && $slot === 10) {
                    Utils::sendConfirmation($player, Rewards::get(Rewards::FRAGMENTGENERATOR), 300000);
                }
            }));

                $menu->send($sender);
            } else {
                $this->xpshopForm($sender);
        }
            return true;
    }

    public function xpshopForm(Player $player) {
        $form = new SimpleForm(function (Player $player, ?int $data) {
            if ($data === null) {
                return;
            }
            switch ($data) {
                case 0:
                    Utils::sendConfirmation($player, Rewards::get(Rewards::FRAGMENTGENERATOR), 300000);
                    break;
                case 1:
                    $player->sendMessage("Second item.");
                    break;
            }
        });
        $form->setTitle("§r§8XP Shop");
        $form->setContent("§r§9Your EXP: §f" . number_format($player->getXpManager()->getCurrentTotalXp()));
        $form->addButton("§r§l§f➥ §1Fragment §fGenerator §r§7(Right Click)\n§r§8300,000 EXP");
        $form->addButton("§r§8Second item.");

        $player->sendForm($form);
    }
}