<?php

namespace wock\NyxCore;

use main\src\poggit\libasynql\libasynql;
use muqsit\invmenu\InvMenuHandler;
use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\item\enchantment\StringToEnchantmentParser;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\TaskHandler;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use wock\NyxCore\Commands\BountyCommand;
use wock\NyxCore\Commands\DustCommand;
use wock\NyxCore\Commands\EnchanterCommand;
use wock\NyxCore\Commands\ExpCommand;
use wock\NyxCore\Commands\FantasyReloadCommand;
use wock\NyxCore\Commands\GameModeCommand;
use wock\NyxCore\Commands\GiftCommand;
use wock\NyxCore\Commands\ItemDBCommand;
use wock\NyxCore\Commands\JackpotCommand;
use wock\NyxCore\Commands\KDRCommand;
use wock\NyxCore\Commands\KitCommand;
use wock\NyxCore\Commands\QuestCommand;
use wock\NyxCore\Commands\RemoveMaskCommand;
use wock\NyxCore\Commands\RTPCommand;
use wock\NyxCore\Commands\ScrollCommand;
use wock\NyxCore\Commands\SettingsCommand;
use wock\NyxCore\Commands\WarpCommand;
use wock\NyxCore\Commands\XPShopCommand;
use wock\NyxCore\Commands\XpWithdrawCommand;
use wock\NyxCore\Events\CratesEvent;
use wock\NyxCore\Events\VanillaEnchantmentsEvent;
use wock\NyxCore\Listeners\EnchantListener;
use wock\NyxCore\Listeners\MasksListener;
use wock\NyxCore\Listeners\RewardsListener;
use wock\NyxCore\Tasks\AnnounceTask;
use wock\NyxCore\Tasks\JackpotRoundTask;
use wock\NyxCore\Utils\Managers\GiftsManager;
use wock\NyxCore\Utils\Managers\JackpotManager;
use wock\NyxCore\Utils\Managers\QuestManager;
use wock\NyxCore\Utils\Managers\SettingsManager;
use wock\NyxCore\Utils\Utils;
use wock\NyxCore\VanillaEnchantments\BaneOfArthropodsEnchantment;
use wock\NyxCore\VanillaEnchantments\DepthStriderEnchantment;
use wock\NyxCore\VanillaEnchantments\LootingEnchantment;
use wock\NyxCore\VanillaEnchantments\SmiteEnchantment;

class Nyx extends PluginBase {

    /** @var Nyx */
    private static Nyx $instance;

    /** @var string[] */
    private array $messages;

    /** @var string[] */
    private array $settings;

    /** @var TaskHandler|null */
    private ?TaskHandler $JackPotRoundTask;

    /** @var int */
    private int $currentIndex;

    /** @var string */
    private string $prefix;

    /** @var bool */
    private bool $usePrefix;

    public const NOPERMISSION = TextFormat::DARK_RED . "You do not have access to that command.";

    protected string $settingsFile;

    protected string $giftsFile;

    public SettingsManager $settingsManager;

    public GiftsManager $giftsManager;

    public function onLoad(): void
    {
        self::$instance = $this;
        $enchants = [
            //new FortuneEnchantment(),
            new LootingEnchantment(),
            new SmiteEnchantment(),
            new BaneOfArthropodsEnchantment(),
            new DepthStriderEnchantment()
        ];
        foreach ($enchants as $enchant) {
            EnchantmentIdMap::getInstance()->register($enchant->getMcpeId(), $enchant);
            StringToEnchantmentParser::getInstance()->register($enchant->getId(), fn() => $enchant);
        }
    }

    public function onEnable(): void
    {
        $this->initializeSettingsManager();
        //$this->initializeGiftManager();
        $this->registerTasks();
        $this->registerCommands();
        $this->registerListeners();
        $this->loadWorlds();
        if (!InvMenuHandler::isRegistered()) {
            InvMenuHandler::register($this);
        }
        foreach ($this->getResources() as $resource) {
            $this->saveResource($resource->getFilename());
        }
        $subdirectories = ["data/", "configurations/"];

        foreach ($subdirectories as $subdirectory) {
            $resourceFiles = $this->getResourcesInSubdirectory($subdirectory);
            foreach ($resourceFiles as $resourceFile) {
                $resourceName = basename($resourceFile);
                $this->saveResource($subdirectory . $resourceName);
            }
        }

        $config = $this->getConfig();
        $this->messages = $config->get("messages", []);
        $this->settings = $config->get("settings", []);
        $this->currentIndex = 0;
        $this->prefix = $config->get("prefix", "[AA]");
        $this->usePrefix = $config->get("use-prefix", true);

        $interval = $config->get("interval", 1200); // Default interval: 60 seconds (20 ticks per second)

        $this->getScheduler()->scheduleRepeatingTask(new AnnounceTask($this), $interval);

        $this->JackPotRoundTask = $this->getScheduler()->scheduleRepeatingTask(
            new JackpotRoundTask(new JackpotManager($this)),
            1200 // 288000
        );
    }

    /**
     * @throws \JsonException
     */
    public function onDisable(): void
    {
        if ($this->JackPotRoundTask !== null) {
            $this->getScheduler()->cancelAllTasks();
        }
    }

    public function registerCommands() {
        $this->getServer()->getCommandMap()->registerAll("nyx", [
            new WarpCommand($this->settingsManager),
            new SettingsCommand($this->settingsManager, $this->settings),
            new GiftCommand(),
            new FantasyReloadCommand($this),
            new KitCommand($this->settingsManager),
            new ItemDBCommand(),
            new RTPCommand($this),
            new GameModeCommand(),
            new RemoveMaskCommand(),
            new BountyCommand(),
            new QuestCommand(new QuestManager(Utils::getConfigurations("quests"))),
            new ScrollCommand($this),
            new EnchanterCommand($this),
            new DustCommand($this),
            new KDRCommand(),
            new XpWithdrawCommand(),
            new ExpCommand($this),
            new XPShopCommand($this->settingsManager),
            new JackpotCommand(new JackpotManager($this)),
        ]);
    }

    public function unregisterCommands() {
        Server::getInstance()->getCommandMap()->unregister(Server::getInstance()->getCommandMap()->getCommand("ban"));
        Server::getInstance()->getCommandMap()->unregister(Server::getInstance()->getCommandMap()->getCommand("ban-ip"));
        Server::getInstance()->getCommandMap()->unregister(Server::getInstance()->getCommandMap()->getCommand("gamemode"));
    }

    public function registerListeners() {
        $pluginMgr = $this->getServer()->getPluginManager();
        $pluginMgr->registerEvents(new NyxListener($this->settingsManager), $this);
        $pluginMgr->registerEvents(new RewardsListener(), $this);
        $pluginMgr->registerEvents(new VanillaEnchantmentsEvent(), $this);
        $pluginMgr->registerEvents(new MasksListener(), $this);
        $pluginMgr->registerEvents(new CratesEvent($this), $this);
        $pluginMgr->registerEvents(new EnchantListener($this), $this);
    }

    public function loadWorlds() {
        $this->getServer()->getWorldManager()->loadWorld("End");
    }

    public function registerTasks() {
       $scheduler = $this->getScheduler();
       //
        //
        //$scheduler->scheduleRepeatingTask(new ScoreboardTask($this), 1);
    }

    public static function getInstance(): Nyx
    {
        return self::$instance;
    }

    public function initializeSettingsManager(): void {
        $this->settingsFile = Utils::getConfigurations("bounty")->getPath();
        $config = new Config($this->settingsFile, Config::JSON);
        $this->settings = $config->get("settings", []);
        $this->settingsManager = new SettingsManager($config);
    }

    /*public function initializeGiftManager(): void{
        $this->giftsFile = $this->getDataFolder() . "gifts.json";
        //$config = new Config($this->giftsFile, Config::JSON);
        $this->giftsManager = new GiftsManager($this->giftsFile);
    }*/

    public function broadcastNextMessage(): void
    {
        if (count($this->messages) > 0) {
            $message = $this->messages[$this->currentIndex];
            if ($this->usePrefix) {
                $message = $this->prefix . $message;
            }
            $formattedMessage = $this->formatMessage($message);

            $players = $this->getServer()->getOnlinePlayers();
            foreach ($players as $player) {
                $isEnabled = $this->settingsManager->isAnnouncementSettingEnabled($player->getName());
                if ($isEnabled) {
                    $player->sendMessage($formattedMessage);
                }

                $this->currentIndex = ($this->currentIndex + 1) % count($this->messages);
            }
        }
    }

    public function formatMessage(string $message): string {
        $message = str_replace("&", "§", $message);
        $message = str_replace("\\n", PHP_EOL, $message);
        return $message;
    }

    private function getResourcesInSubdirectory($subdirectory): array
    {
        $resourceFiles = [];
        $directory = $this->getFile() . "resources/" . $subdirectory;

        if (is_dir($directory)) {
            $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));
            foreach ($iterator as $file) {
                if ($file->isFile()) {
                    $resourceFiles[] = $file->getPathname();
                }
            }
        }

        return $resourceFiles;
    }
}