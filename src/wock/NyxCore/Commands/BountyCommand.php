<?php

namespace wock\NyxCore\Commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use wock\NyxCore\Nyx;
use wock\NyxCore\Utils\Managers\BountyManager;
use wock\NyxCore\Utils\Utils;

class BountyCommand extends Command {

    public function __construct()
    {
        parent::__construct("bounty", "View all bounty subcommands", "/bounty help", ["b"]);
        $this->setPermission("Nyx.bounty");
    }

    /**
     * @throws \JsonException
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): bool
    {
        if (!$sender instanceof Player) {
            $sender->sendMessage("This command can only be used in-game.");
            return false;
        }

        if (empty($args)) {
            $sender->sendMessage("§r§l§6[!] §r§6Bounty Commands:");
            $sender->sendMessage("§r§e/bounty add <player> <amount> §f- §7Add a bounty to a player");
            $sender->sendMessage("§r§e/bounty leaderboard §f- §7View the 10 highest bounties");
            $sender->sendMessage("§r§e/bounty view [player] §f- §7View your or another players bounty statistics");
            if ($sender->hasPermission("Nyx.bounty.remove")) {
                $sender->sendMessage("§r§e/bounty remove §f- §7Remove a bounty from a player.");
            }
            return false;
        }

        $bountyManager = new BountyManager(Utils::getConfigurations("bounty"));
        $subcommand = strtolower(array_shift($args));

        switch ($subcommand) {
            case "add":
            case "set":
            case "give":
                if (count($args) < 2) {
                    $sender->sendMessage("Usage: /bounty add <player> <amount>");
                    return false;
                }

                $targetPlayerName = array_shift($args);

                // Parse the shorthand amount using the custom function
                $amountShorthand = array_shift($args);
                $amount = Utils::parseShorthandAmount($amountShorthand);

                $targetPlayer = Utils::customGetPlayerByPrefix($targetPlayerName);

                if ($targetPlayer !== null) {
                    $bountyManager->setBounty($sender, $targetPlayer, $amount);
                    $sender->getServer()->broadcastMessage("§r§l§6[!] §r{$sender->getName()} §r§7has added a bounty of §r$" . number_format($amount) . " §7to §r{$targetPlayer->getName()}§r§7.");
                    $totalBounty = $bountyManager->getBounty($targetPlayer->getName()) + $amount;
                    $sender->getServer()->broadcastMessage("§r§7§oTotal Bounty: $" . number_format($totalBounty) . " (-10%)");
                } else {
                    $sender->sendMessage("§r§l§c[!] §r§cPlayer '$targetPlayerName' not found or is not online.");
                }
                break;
            case "remove":
                if (!$sender->hasPermission("Nyx.bounty.remove")) {
                    $sender->sendMessage(Nyx::NOPERMISSION);
                    return false;
                }
                if (count($args) !== 1) {
                    $sender->sendMessage("Usage: /bounty remove <player>");
                    return false;
                }
                $targetPlayerName = array_shift($args);
                $targetPlayer = Utils::customGetPlayerByPrefix($targetPlayerName);
                if ($targetPlayer === null) {
                    $sender->sendMessage("§r§l§c[!] §r§cPlayer '$targetPlayerName' not found or is not online.");
                } else {
                    $targetPlayerBounty = $bountyManager->getBounty($targetPlayer->getName());
                    if ($targetPlayerBounty <= 0) {
                        $sender->sendMessage("§r§l§c[!] §r§c'$targetPlayerName' does not have a bounty.");
                    } else {
                        $bountyManager->removeBounty($targetPlayer->getName());

                        $sender->sendMessage("§r§l§a[!] §r§aBounty for '$targetPlayerName' has been removed.");
                    }
                }
                break;
            case "top":
            case "leaderboard":
                $bountyData = [];

                foreach ($sender->getServer()->getOnlinePlayers() as $player) {
                    $playerName = $player->getName();
                    $bountyAmount = $bountyManager->getBounty($playerName);
                    $bountyData[$playerName] = $bountyAmount;
                }

                arsort($bountyData);

                $topPlayers = array_slice($bountyData, 0, 10);

                $sender->sendMessage("§6Top 10 Bounty Leaderboard:");
                $position = 1;
                foreach ($topPlayers as $playerName => $bountyAmount) {
                    $sender->sendMessage("§e#$position: $playerName - $" . number_format($bountyAmount));
                    $position++;
                }
                break;
            case "view":
                if (empty($args)) {
                    $bounty = $bountyManager->getBounty($sender->getName());
                    $sender->sendMessage("§r§l§6[!] §r§fYour current bounty: $" . number_format($bounty));
                } else {
                    $targetPlayerName = array_shift($args);
                    $viewTargetPlayer = Utils::customGetPlayerByPrefix($targetPlayerName);

                    if ($viewTargetPlayer !== null) {
                        $bounty = $bountyManager->getBounty($viewTargetPlayer->getName());
                        $sender->sendMessage("§r§l§6[!] §r§f{$viewTargetPlayer->getName()}'s current bounty: $" . number_format($bounty));
                    } else {
                        $sender->sendMessage("§r§l§c[!] §r§cPlayer $targetPlayerName not found or is not online.");
                    }
                }
                break;

            default:
                $sender->sendMessage("Unknown subcommand. Usage: /bounty [subcommand] [args]");
                break;
        }

        return true;
    }
}