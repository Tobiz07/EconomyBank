<?php

namespace Sh00ckBass\EconomyBank\Events;

use pocketmine\event\Listener;
use pocketmine\utils\Config;
use Sh00ckBass\EconomyBank\Main;
use pocketmine\event\player\PlayerDeathEvent;
use onebone\economyapi\EconomyAPI;

/*******************************************************************************
 Urheberrechtshinweis
 Copyright © Tobias Zechmann 2020
 
 Alle Inhalte dieses Quelltextes sind urheberrechtlich geschützt.
 Das Urheberrecht liegt, soweit nicht ausdrücklich anders gekennzeichnet,
 bei Tobias Zechmann. Alle Rechte vorbehalten.
 Jede Art der Vervielfältigung, Verbreitung, Vermietung, Verleihung,
 öffentlichen Zugänglichmachung oder andere Nutzung
 bedarf der ausdrücklichen, schriftlichen Zustimmung von Tobias Zechmann.
 *******************************************************************************/

class DeathEvent implements Listener
{
    
    private $plugin;
    
    public function __construct(Main $plugin)
    {
        $this->plugin = $plugin;
    }
    
    public function onJoin(PlayerDeathEvent $e)
    {
        $player = $e->getPlayer();
        $config = new Config($this->plugin->getDataFolder() . "config.yml", Config::YAML);
        $moneytopay = $config->get("moneytopay");
        if($moneytopay == "0") return true;
        if((EconomyAPI::getInstance()->myMoney($player) - $moneytopay) >= 0) {
            EconomyAPI::getInstance()->reduceMoney($player, $moneytopay);
        }
    }
    
}