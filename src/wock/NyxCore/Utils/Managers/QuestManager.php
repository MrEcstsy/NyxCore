<?php

namespace wock\NyxCore\Utils\Managers;

use pocketmine\player\Player;
use pocketmine\utils\Config;

class QuestManager {

    protected Config $questConfig;

    public function __construct(Config $questConfig){
        $this->questConfig = $questConfig;
    }

    /**
     * Check if a player has completed a quest.
     *
     * @param Player $player
     * @param string $questId
     * @return bool
     */
    public function hasCompletedQuest(Player $player, string $questId): bool {
        if ($this->questConfig->exists($questId)) {
            $questData = $this->questConfig->get($questId);
            // Implement your logic to check if the player has completed the quest
            // Compare player progress with quest objectives
            // Return true if completed, false otherwise
        }
        return false;
    }

    /**
     * Mark a quest as completed for a player.
     *
     * @param Player $player
     * @param string $questId
     */
    public function completeQuest(Player $player, string $questId): void {
        // Mark the specified quest as completed for the player
        // Update the player's quest progress or rewards
    }

    /**
     * Get a list of available quests for a player.
     *
     * @param Player $player
     * @return array
     */
    public function getAvailableQuests(Player $player): array {
        // Retrieve a list of quests available to the player
        // You can filter quests based on player's level, faction, etc.
    }

    /**
     * Start a quest for a player.
     *
     * @param Player $player
     * @param string $questId
     */
    public function startQuest(Player $player, string $questId): void {
        // Start the specified quest for the player
        // Initialize quest objectives, provide instructions, etc.
    }

    /**
     * Check if a player has met the objectives of a quest.
     *
     * @param Player $player
     * @param string $questId
     * @return bool
     */
    public function hasMetQuestObjectives(Player $player, string $questId): bool {
        // Check if the player has met all objectives of the quest
        // Evaluate player's progress and update as needed
    }

    /**
     * Reward a player for completing a quest.
     *
     * @param Player $player
     * @param string $questId
     */
    public function rewardPlayer(Player $player, string $questId): void {
        // Reward the player for completing the quest
        // Grant in-game items, currency, faction reputation, etc.
    }

    /**
     * Create a new quest and add it to the quest configuration.
     *
     * @param string $questId
     * @param string $questName
     * @param string $questType
     * @param string $description
     * @param int $targetAmount
     * @param array $rewards
     * @throws \JsonException
     */
    public function createQuest(string $questId, string $questName, string $questType, string $description, int $targetAmount, array $rewards) : void {
        $questData = [
            'name' => $questName,
            'stages' => [
                '1' => [
                    'action' => $questType,
                    'details' => $description,
                    'amount' => $targetAmount,
                ],
            ],
            'commands-executed-on-complete' => [], // Add commands here if needed
        ];

        $this->questConfig->set($questId, $questData);
        $this->questConfig->save();
    }

    public function trackQuest(Player $sender, array $args): void {
        // Check if a quest ID is provided in $args and call the QuestManager to track it
        // Example: /quest track <questID>
    }
}