<?php

namespace xtcy\hometesting\listeners;

use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerQuitEvent;
use xtcy\hometesting\Loader;
use xtcy\hometesting\Utils\Home;

class EventListener implements \pocketmine\event\Listener
{

    public function __construct(public Loader $plugin) {

    }

    public function onPlayerLogin(PlayerLoginEvent $event) {
        $player = $event->getPlayer();
        if (Home::getInstance()->getHomeSession($player) === null) {
            Home::getInstance()->createHomes($player);
        }
    }

    public function onPlayerJoin(PlayerJoinEvent $event): void
    {
        $player = $event->getPlayer();

        Home::getInstance()->getHomeSession($player)->setConnected(true);
    }

    public function onPlayerQuit(PlayerQuitEvent $event): void
    {
        $player = $event->getPlayer();
        $session = Home::getInstance()->getHomeSession($player);

        $session->setConnected(false);
    }
}