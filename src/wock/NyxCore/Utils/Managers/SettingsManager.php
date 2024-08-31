<?php

namespace wock\NyxCore\Utils\Managers;

use pocketmine\item\VanillaItems;
use pocketmine\utils\Config;

class SettingsManager {

    private Config $config;
    private array $settings;

    public function __construct(Config $config) {
        $this->config = $config;
        $this->settings = $this->config->getAll();
    }

    public function isChestGUISettingEnabled(string $playerName): bool {
        return $this->settings[$playerName]["chestguis"] ?? false;
    }

    public function isAnnouncementSettingEnabled(string $playerName): bool {
        return $this->settings[$playerName]["announcements"] ?? false;
    }

    /**
     * @throws \JsonException
     */
    public function setChestGUISetting(string $playerName, bool $value): void {
        $this->settings[$playerName]["chestguis"] = $value;
        $this->saveSettings();
    }

    /**
     * @throws \JsonException
     */
    public function setAnnouncementSetting(string $playerName, bool $value): void {
        $this->settings[$playerName]["announcements"] = $value;
        $this->saveSettings();
    }

    public function hasPlayerData(string $playerName): bool {
        return isset($this->settings[$playerName]);
    }

    /**
     * @throws \JsonException
     */
    public function createPlayerData(string $playerName): void {
        if (!$this->hasPlayerData($playerName)) {
            $this->settings[$playerName] = [
                "chestguis" => true,
                "announcements" => true
            ];
            $this->saveSettings();
        }
    }

    /**
     * @throws \JsonException
     */
    private function saveSettings(): void {
        // Remove the "players" section from the settings
        unset($this->settings["players"]);

        $this->config->setAll($this->settings);
        $this->config->save();
    }
}
