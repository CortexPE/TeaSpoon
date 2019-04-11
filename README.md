<h1>TeaSpoon<img src="https://raw.githubusercontent.com/CortexPE/stuff/master/TeaSpoonLogo.png" height="64" width="64" align="left"></img></h1>
<br />

[![Poggit](https://poggit.pmmp.io/ci.shield/CortexPE/TeaSpoon/~)](https://poggit.pmmp.io/ci/CortexPE/TeaSpoon/~) [![Donate](https://img.shields.io/badge/donate-PayPal-yellow.svg?style=flat-square)](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=MABFZPDR8F5UG) [![HitCount](http://hits.dwyl.io/CortexPE/TeaSpoon.svg)](http://hits.dwyl.io/CortexPE/TeaSpoon) [![License](https://img.shields.io/badge/license-AGPL%20v3-blue.svg?style=flat-square)](https://github.com/CortexPE/TeaSpoon/blob/master/LICENSE) [![Discord](https://img.shields.io/discord/350333413737365522.svg?style=flat-square&label=discord&colorB=7289da)](https://discord.gg/t5NsTyj)

A Massive PocketMine-MP plugin designed and is aiming to extend PMMP's functionalities (Without completely changing it) to Make it more Vanilla-Like.

I wouldn't provide any support for using other branches of the plugin. They're still under development and very experimental. I won't be held responsible for any damages or corruptions that occured by using unsupported branches.

A/N: If you don't like it / hate it, Then don't even bother using it. It's that simple... TeaSpoon was made for those who need it. :wink:

Contributions are very welcome :smile:<br />You may contribute by opening a Pull Request and if it has been proven to be correct & working, I'll surely merge it.

__[Discord Group to 'talk-to-a-human-for-support' and for questions](https://discord.gg/t5NsTyj)__

# Keep the project alive!
<p align="center"><strong>Big thanks to RedCraftGH for supporting the project :smile:</strong></p>


Help keep me motivated into making this massive thing... Maintaining it is sometimes a pain and I only find it a waste of time to update something I don't get anything in return for :/ I hope you all can understand. You can donate here: [![Donate](https://img.shields.io/badge/donate-PayPal-yellow.svg?style=flat-square)](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=MABFZPDR8F5UG)

# Installation
Installation is easy, Just download the latest phar from [Poggit](https://poggit.pmmp.io/ci/CortexPE/TeaSpoon/~) then put it to your ```./plugins/``` folder. Restart your server. And you're basically done.
***This plugin will only work on stable PMMP releases, Any issues regarding 4.0.0 (development builds) will be closed***

# Issue Reporting
 - ALWAYS use the [LATEST PocketMine-MP Build](https://jenkins.pmmp.io/job/PocketMine-MP/lastSuccessfulBuild/artifact/) to use this plugin
 - Remove any plugins that may interfere with TeaSpoon's current features (if the feature cannot be disabled in the config.yml file)
 - Including the "Steps to Reproduce" in the issue report would be really helpful in fixing bugs.
 - To help me determine why the issue occurs, including the TeaSpoonDump from ```/bugreport``` lets me view all the necessary server information to easily determine incompatibility issues and mis-configuration issues. <sub>Privacy Concerns? You can check the code for yourself <a href="https://github.com/CortexPE/TeaSpoon/blob/master/src/CortexPE/commands/BugReportCommand.php">here</a> ;)</sub>
 - The only supported branch is PMMP's 'master' branch. Issues regarding other branches will be closed.

# FAQs
### MobAI:
&nbsp;&nbsp;&nbsp;&nbsp;For now, you can use [PureEntitiesX](https://github.com/RevivalPMMP/PureEntitiesX) while disabling ```entities.register``` in TeaSpoon's Configuration File.
### Redstone System:
&nbsp;&nbsp;&nbsp;&nbsp;Being worked on... :wink:

***[Read More...](https://github.com/CortexPE/TeaSpoon/wiki#faqs)***

# Finished & Planned Features
 - Worlds
  - [X] Dimensions
    - [X] Nether Dimension
    - [X] End Dimension
    - [X] Fully Functional Nether Portal Frame and Block
    - [X] Funtional END_PORTAL Block (Portal Soon)
  - [X] Weather System
  - [X] Temperature System
 - Blocks
   - [X] EndPortal
   - [X] Portal (Nether Portal Block)
   - [X] DragonEgg
   - [X] Beacon
   - [X] SlimeBlock
   - [X] Vanilla-Like MobSpawner (Credits: [XenialDan](https://github.com/thebigsmileXD))
   - [X] Working Shulker Boxes
   - [X] Hoppers
   - [X] (somewhat working) Anvils [needs fix] // Handling is translated from [NukkitX](https://github.com/NukkitX/Nukkit)
   - [X] Enchantment Tables // Handling is translated from [NukkitX](https://github.com/NukkitX/Nukkit)
   - [X] SnowLayer (Affected by rain and temperature)
   - [X] Pumpkin / Jack o' Lantern (Spawns Golems)
   - [X] Brewing Stand
   - [X] Cauldron
 - Items
   - [X] Vanilla Enchants (Progress: 98% | Credits to [TheAz928](https://github.com/TheAz928) for some of the values)
   - [X] Armor Damage
   - [X] Armor Stands
   - [X] FireCharge
   - [X] Fully Functional Elytra Wings
   - [X] Fully Functional Fireworks (Credits to [XenialDan](https://github.com/thebigsmileXD) for 45% of it)
   - [X] Lingering Potions (Credits: [ClearSkyTeam](https://github.com/ClearSkyTeam))
   - [X] Chorus Fruit (with customizable Delay)
   - [X] FishingRod (Fully working Fishing System)
   - [X] Vanilla-Like "Instant-Armor-Equipment"
   - [X] Lightning Rods
   - [X] Dragon Breath
   - [X] Trident
 - Entities & Mobs
   - [X] XP Drops
   - [X] Projectiles
     - [X] Tipped Arrows
     - [X] LingeringPotion
   - [ ] Entities
     - [X] Lightning
     - [X] EndCrystal
   - [X] Mobs
     - [X] Bat
     - [X] Blaze
     - [X] CaveSpider
     - [X] Chicken
     - [X] Cow
     - [X] Creeper
       - [X] Charged Creepers
       - [X] Ignited Creepers
     - [X] Donkey
     - [X] ElderGuardian
     - [X] EnderDragon
     - [X] Enderman
     - [X] Endermite
     - [X] Evoker
     - [X] Ghast
     - [X] Guardian
     - [X] Horse
     - [X] Husk
     - [X] IronGolem
     - [X] Llama
     - [X] MagmaCube
     - [X] Mooshroom
     - [X] Mule
     - [X] Ocelot
     - [X] Parrots
     - [X] Pig
     - [X] PolarBear
     - [X] Rabbit
     - [X] Sheep
     - [X] Shulker
     - [X] Silverfish
     - [X] Skeleton
     - [X] Skeleton Horse
     - [X] Slime
     - [X] SnowGolem (Affected by Rain & Temperature)
     - [X] Spider
     - [X] Stray
     - [X] Vex
     - [X] Vindicator
     - [X] Witch
     - [X] Wither
     - [X] WitherSkeleton
     - [X] Wolf
     - [X] Zombie Horse
     - [X] Zombie Pigman
     - [X] Zombie Villager
 - Commands
   - [X] More Vanilla-Like /kill command (Not perfect)
   - [X] World Command
   - [X] Clear Command
   - [X] PlaySound Command
 - Mechanics
   - [X] Swimming
 - Utils
   - [X] TextFormat::center like PC or MiNET. (Credits: [Turanic](https://github.com/TuranicTeam/Turanic))
<br />***More to do...***
