<?php

namespace wock\NyxCore\Commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;
use wock\NyxCore\Nyx;

class GiftCommand extends Command {

    public function __construct()
    {
        parent::__construct("gift", "Send gifts to players", "/gift");
        $this->setPermission("Nyx.gift");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if (!$sender instanceof Player) {
            $sender->sendMessage("You must run this command in-game");
            return false;
        }

        if (empty($args)) {
            // /gift command without any arguments
            $sender->sendMessage("Gift commands:");
            $sender->sendMessage("/gift <player> - Send the item in your hand as a gift to the specified player");
            $sender->sendMessage("/gift toggle - Toggle receiving gifts");
            $sender->sendMessage("/gifts - View and manage your received gifts");
            return true;
        }

        $subCommand = strtolower(array_shift($args));
        switch ($subCommand) {
            case "toggle":
                // Handle /gift toggle
                Nyx::getInstance()->giftsManager->toggleGifts($sender);
                break;

            default:
                // Assume it's a player name and treat it as /gift <player>
                $recipientName = $subCommand;
                $recipient = Server::getInstance()->getPlayerByPrefix($recipientName);

                if ($recipient === null) {
                    $sender->sendMessage("Player $recipientName not found");
                    return true;
                }

                Nyx::getInstance()->giftsManager->sendGift($sender, $recipient);
                break;
        }

        return true;
    }

}