<?php

namespace wock\NyxCore\Commands;

use muqsit\invmenu\InvMenu;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\item\Item;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;
use wock\NyxCore\Nyx;
use wock\NyxCore\Utils\Managers\SettingsManager;

class GiftsCommand extends Command {

    public SettingsManager $settingsManager;

    public function __construct(SettingsManager $settingsManager)
    {
        parent::__construct("gifts", "View your gift box", "/gifts");
        $this->setPermission("Nyx.gifts");
        $this->settingsManager = $settingsManager;

    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if (!$sender instanceof Player) {
            $sender->sendMessage("You must run this command in-game");
            return false;
        }

        if ($this->settingsManager->isChestGUISettingEnabled($sender)) {
            $giftMenu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
            $giftMenu->setName("§r§l§8Gifts Menu");

            $data = Nyx::getInstance()->giftsManager->loadData();
            $gifts = $data[$sender->getName()] ?? [];

            foreach ($gifts as $giftData) {
                $item = $this->arrayToItem($giftData);
                if ($item !== null) {
                    $giftMenu->getInventory()->addItem($item);
                }
            }

            $giftMenu->send($sender);
        } else {
            // Handle the case when the Chest GUI setting is disabled
        }

        return true;
    }
}