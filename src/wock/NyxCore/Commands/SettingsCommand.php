<?php

namespace wock\NyxCore\Commands;

use JsonException;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use wock\NyxCore\Nyx;
use wock\NyxCore\Utils\Managers\SettingsManager;
use wock\NyxCore\Utils\Utils;

class SettingsCommand extends Command {

    public SettingsManager $settingsManager;

    public function __construct(SettingsManager $settingsManager, array $settings)
    {
        parent::__construct("settings", "Modify your server settings to make the experience more enjoyable.", "/settings <setting> <value>");
        $this->setPermission("Nyx.settings");
        $this->settingsManager = $settingsManager;
    }

    /**
     * @throws JsonException
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): bool
    {
        if (!$sender instanceof Player) {
            $sender->sendMessage(TextFormat::colorize($this->getConfig()->getNested("settings.in-game-only")));
            return false;
        }

        if (count($args) === 0) {
            $sender->sendMessage(TextFormat::colorize($this->getConfig()->getNested("settings.invalid-usage")));
            return false;
        }

        $subCommand = strtolower($args[0]);
        $senderName = $sender->getName();

        if ($subCommand === "list") {
            $this->listSettings($sender);
            return true;
        }

        if ($subCommand === "chestguis") {
            if (count($args) === 1) {
                $this->swapChestGUISetting($sender);
                return true;
            } elseif (count($args) === 2) {
                $value = strtolower($args[1]);
                $this->setChestGUISetting($senderName, $value);
                return true;
            }
        }

        if ($subCommand === "announcements") {
            if (count($args) === 1) {
                $this->swapAnnouncementSetting($sender);
                return true;
            } elseif (count($args) === 2) {
                $value = strtolower($args[1]);
                $this->setAnnouncementSetting($senderName, $value);
                return true;
            }
        }

        $sender->sendMessage(TextFormat::colorize($this->getConfig()->getNested("settings.invalid-subcommand")));
        return true;
    }

    private function swapChestGUISetting(Player $player): void {
        $settingsManager = $this->getSettingsManager();
        $currentValue = $settingsManager->isChestGUISettingEnabled($player->getName());
        $newValue = !$currentValue;
        $settingsManager->setChestGUISetting($player->getName(), $newValue);
        $player->sendMessage(TextFormat::colorize($this->getConfig()->getNested("settings.chestguis-setting-changed") . ($newValue ? $this->getConfig()->getNested("settings.enabled") : $this->getConfig()->getNested("settings.disabled"))));
    }

    private function swapAnnouncementSetting(Player $player): void {
        $settingsManager = $this->getSettingsManager();
        $currentValue = $settingsManager->isAnnouncementSettingEnabled($player->getName());
        $newValue = !$currentValue;
        $settingsManager->setAnnouncementSetting($player->getName(), $newValue);
        $player->sendMessage(TextFormat::colorize($this->getConfig()->getNested("settings.announcements-setting-changed") . ($newValue ? $this->getConfig()->getNested("settings.enabled") : $this->getConfig()->getNested("settings.disabled"))));
    }

    private function setAnnouncementSetting(string $player, string $value): void
    {
        if ($value === "true") {
            $newValue = true;
        } elseif ($value === "false") {
            $newValue = false;
        } else {
            if ($player instanceof Player) {
                $player->sendMessage(TextFormat::colorize($this->getConfig()->getNested("settings.invalid-value", "invalid value, use true or false for settings.")));
                return;
            }
        }

        if ($player instanceof Player) {
            $settingsManager = $this->getSettingsManager();
            $settingsManager->setAnnouncementSetting($player->getName(), $newValue);
            $player->sendMessage(TextFormat::colorize($this->getConfig()->getNested("settings.announcements-setting-changed", "Announcements setting is now ") . ($newValue ? $this->getConfig()->getNested("settings.enabled", "enabled") : $this->getConfig()->getNested("settings.disabled", "disabled"))));
        }
    }

    private function setChestGUISetting(string $player, string $value): void
    {
        if ($value === "true") {
            $newValue = true;
        } elseif ($value === "false") {
            $newValue = false;
        } else {
            if ($player instanceof Player) {
                $player->sendMessage(TextFormat::colorize($this->getConfig()->getNested("settings.invalid-value")));
                return;
            }
        }

        if ($player instanceof Player) {
            $settingsManager = $this->getSettingsManager();
            $settingsManager->setChestGUISetting($player, $newValue);
            $player->sendMessage(TextFormat::colorize($this->getConfig()->getNested("settings.chestguis-setting-changed") . ($newValue ? $this->getConfig()->getNested("settings.enabled", "enabled") : $this->getConfig()->getNested("settings.disabled", "disabled"))));
        }
    }

    public function listSettings(Player $player): void {
        $player->sendMessage(TextFormat::colorize($this->getConfig()->getNested("settings.available-settings")));

        $availableSettingsList = $this->getConfig()->getNested("settings.available-settings-list", ["- chestguis", "- announcements"]);
        foreach ($availableSettingsList as $setting) {
            $player->sendMessage(TextFormat::colorize($setting));
        }
    }

    public function getSettingsManager(): SettingsManager {
        return $this->settingsManager;
    }

    public function getConfig(): Config {
        return Utils::getConfigurations("messages");
    }
}
