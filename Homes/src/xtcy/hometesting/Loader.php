<?php

namespace xtcy\hometesting;

use pocketmine\plugin\PluginBase;
use poggit\libasynql\DataConnector;
use poggit\libasynql\libasynql;
use xtcy\hometesting\commands\DelHome;
use xtcy\hometesting\commands\HomeCommand;
use xtcy\hometesting\commands\ListHomes;
use xtcy\hometesting\commands\SetHome;
use xtcy\hometesting\listeners\EventListener;
use xtcy\hometesting\Utils\Home;
use xtcy\hometesting\Utils\QueryConstants;

class Loader extends PluginBase
{

    private static DataConnector $database;

    public static Home $sessionManager;

    public static Loader $loader;

    protected function onLoad(): void
    {
        self::$loader = $this;
    }

    public function onEnable(): void
    {
        $this->saveDefaultConfig();
        $settings = [
            "type" => "sqlite",
            "sqlite" => ["file" => "sqlite.sql"],
            "worker-limit" => 1
        ];

        self::$database = libasynql::create(self::getInstance(), $settings, ["sqlite" => "sqlite.sql"]);
        self::$database->executeGeneric(QueryConstants::PLAYERS_INIT);
        self::$database->waitAll();

        self::$sessionManager = new Home($this);
        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
        $this->getServer()->getCommandMap()->registerAll("hometesting", [
            new ListHomes($this, "listhomes", "See all your homes", ["homes"]),
            new SetHome($this, "sethome", "Set a home"),
            new DelHome($this, "delhome", "Delete a home"),
            new HomeCommand($this, "home", "Teleport to home")
        ]);
    }

    public static function getInstance(): Loader {
        return self::$loader;
    }

    public static function getDatabase() : DataConnector
    {
        return self::$database;
    }

    public static function getSessionManager() : Home
    {
        return self::$sessionManager;
    }
}