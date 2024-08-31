<?php

namespace wock\NyxCore\Commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use wock\NyxCore\Utils\Managers\QuestManager;

class QuestCommand extends Command {

    protected QuestManager $questManager;

    public function __construct(QuestManager $questManager){
        parent::__construct("quest", "View quest commands", "/quest help", ["quests"]);
        $this->setPermission("Nyx.quest");
        $this->questManager = $questManager;
    }

    /**
     * @throws \JsonException
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): bool
    {
        if (!$sender instanceof Player) {
            $sender->sendMessage("You must run this in-game.");
            return false;
        }

        if (!$this->testPermission($sender)) {
            return false;
        }

        if (empty($args)) {
            // Display a message with available subcommands
            $sender->sendMessage("Available subcommands: add, list, track");
            return true;
        }

        $subcommand = strtolower(array_shift($args));

        switch ($subcommand) {
            case "add":
                if (count($args) < 6) {
                    $sender->sendMessage("Usage: /quest add <questID> <questName> <questType> <description> <targetAmount> <rewards...>");
                } else {
                    $questId = array_shift($args);
                    $questName = array_shift($args);
                    $questType = array_shift($args);
                    $description = array_shift($args);
                    $targetAmount = intval(array_shift($args));
                    $rewards = $args; // The remaining arguments are rewards and can be an array
                    $this->questManager->createQuest($questId, $questName, $questType, $description, $targetAmount, $rewards);

                    $sender->sendMessage("Quest '$questName' ($questId) added successfully.");
                }
                break;
            case "list":
                $this->questManager->getAvailableQuests($sender);
                break;
            case "track":
                $this->questManager->trackQuest($sender, $args);
                break;
            default:
                $sender->sendMessage("Invalid subcommand. Available subcommands: add, list, track");
                break;
        }

        return true;
    }

}