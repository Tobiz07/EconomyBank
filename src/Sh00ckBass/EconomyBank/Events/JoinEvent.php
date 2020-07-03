<?php
namespace Sh00ckBass\EconomyBank\Events;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\utils\Config;
use Sh00ckBass\EconomyBank\Main;

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

class JoinEvent implements Listener
{
    
    private $plugin;
    
    public function __construct(Main $plugin)
    {
        $this->plugin = $plugin;
    }
    
    public function onJoin(PlayerJoinEvent $e) 
    {
        $p = $e->getPlayer();
        if(!file_exists($this->plugin->getDataFolder() . "money/" . "money.yml")) {
            $config = new Config($this->plugin->getDataFolder() . "money/" . "money.yml", Config::YAML);
            $config->set($p->getUniqueId()->toString(), 0);
            $config->save();
        }else {
            $config = new Config($this->plugin->getDataFolder() . "money/" . "money.yml", Config::YAML);
            if(!$config->exists($p->getUniqueID()->toString())) {
                $config->set($p->getUniqueId()->toString(), 0);
                $config->save();
            }
        }
    }
    
}

