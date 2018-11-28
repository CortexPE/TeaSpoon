<?php

/**
 *
 * MMP""MM""YMM               .M"""bgd
 * P'   MM   `7              ,MI    "Y
 *      MM  .gP"Ya   ,6"Yb.  `MMb.   `7MMpdMAo.  ,pW"Wq.   ,pW"Wq.`7MMpMMMb.
 *      MM ,M'   Yb 8)   MM    `YMMNq. MM   `Wb 6W'   `Wb 6W'   `Wb MM    MM
 *      MM 8M""""""  ,pm9MM  .     `MM MM    M8 8M     M8 8M     M8 MM    MM
 *      MM YM.    , 8M   MM  Mb     dM MM   ,AP YA.   ,A9 YA.   ,A9 MM    MM
 *    .JMML.`Mbmmd' `Moo9^Yo.P"Ybmmd"  MMbmmd'   `Ybmd9'   `Ybmd9'.JMML  JMML.
 *                                     MM
 *                                   .JMML.
 * This file is part of TeaSpoon.
 *
 * TeaSpoon is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * TeaSpoon is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with TeaSpoon.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author CortexPE
 * @link https://CortexPE.xyz
 *
 */

declare(strict_types = 1);

namespace CortexPE;

class Splash {

	// tbh, I just added splashes for fun... never thought I would be making a completely different class just for splash texts xD

	public const VALENTINES_SPLASH = "Happy Valentines Day!";
	/** @var string[] */
	private static $TEASPOON_SPLASHES = [
		'Low-Calorie blend', // first ever teaspoon splash text... and that's why its in ' not " xd
		"Don't panic! Have a cup of tea",
		"In England, Everything stops for tea",
		"Fueled by Music and Coffee",
		"A E S T H E T H I C S",
		"#BlameShoghi",
		"#BlameMojang",
		"#BlamePMMP",
		"ERMAHGERD",
		"Written in PHP!",
		"This is a splash text.",
		"ONE LOVE",
		"rip.",
		"This splash text is a joke.",
		"SUPERCALIFRAGILISTICEXPIALIDOCIOUS!",
		"Well this exists.",
		"IE EXISTS TO DOWNLOAD CHROME!",
		"I might have killed it.",
		"We have VCS Systems. :P",
		"We have *crappy* VCS Systems. :P",
		"¯\_(ツ)_/¯",
		"Fukkit!",
		"§4R§cA§6I§eN§2B§aO§bW§3 T§1E§9X§dT§5!",
		"@TheAz928 is notorious for HardCoding values!",

		// SoftwareGore from: Best of r/SoftwareGore -- https://www.youtube.com/watch?v=kekn2HhE-qI  *I'M DYING*
		"DAMMIT STEVE",
		"Installing Dragon Center Update 147%",
		"The Power Saver app may drain the battery.",
		":( Your PC ran and We're jus... For more anforma... If you call suppor... DRI",
		"Could not complete your request because Brendan's an idiot.",
		"CONGRATULATIONS YOU GOT THEM ALL WRONG!!!",
		"SHAKESPEARE QUOTE OF THE DAY: An SSL error has occured and a secure connection to the server cannot be made.",
		"Russia is located in Russia",
		":( Your PC ra We're (0% Complete)",
		"We all know there are nine genders.",
		"Do you really want to exist without saving?",
		"Something Happened. SOMETHING HAPPENED!!!!!",
		"??? ???",
		"This Driver can't",
		"Amazing Russian Bombshells Want To Date You!",
		":( Your P",
		"Tip of the Day: Chc xnt j mnv ---",
		"Java Update???? Java??????????????????????",
		"Please wait while OneNote inserts the d...",
		":( Yo",
		"Fufufu fufufu fufufu fufufu fufufu",
		"Task Manager (Not Responding)",

		// Best of r/CrappyDesign https://www.youtube.com/watch?v=QeXs5NyX5WI
		"VICIOUS INCEST 2015",
		"HEAL THY BUR GERS",
		"NOTHING IS POSSIBLE",
		"SASA LELE",
		"baby needs beers & wines",
		"PLEASE NO SMOKING FOOD RADIOS WITHOUT HEADPHONES BICYCLES",
		"BOY & MOM SAMPSON",
		"QUIEF ZONE",
		" - Cyborg Babies -",
		"SO MA UL TE",
		"DEFORMED CAR",
		"Nesquick from the Nesdi**!",
		"THE CUMMY",
		"NOW HIRING NOW RIGHT NOW WE'RE HIRING NOW",
		"BLONK",
		"Stairs & Elevators & Terminal & Stairs & Elevators & Terminal & Stairs",
		"COTTON CHICKEN CANDY NUGGETS",
		"FIND A COLON NEAR YOU",
		"It's NOT Its ME YOU",
		"DO NOT BRING FOOD OR DRINK IN LAB - STOP - NO - FOOD OR DRINK - ALLOWED - IN LAB",
	];

	/** @var string[] */
	private static $CHRISTMAS_SPLASHES = [
		"Ho Ho Ho...",
		"Merry Christmas!",
	];

	public static function getRandomSplash(): string{
		if(self::isCortexsBirthday()){
			return (mt_rand(1, 2) == 1 ? "Cortex's biological age is now " . strval(intval(date('Y')) - 1999) . "!" : "Happy birthday Cortex!"); // lolz
		}
		if(self::isChristmastide()){
			return self::$CHRISTMAS_SPLASHES[array_rand(self::$CHRISTMAS_SPLASHES)];
		}
		if(self::isValentines() && mt_rand(1, 2) == 2){
			return self::VALENTINES_SPLASH;
		}
		if(self::isWednesday() && mt_rand(1, 2) == 1){
			return "It's WEDNESDAY my dudes.";
		}

		return self::getRandomTSPSplash();
	}

	public static function isWednesday(): bool{
		return (date('w') == 3);
	}

	public static function isChristmastide(): bool{
		$month = date('n');
		$day = date('j');

		return ($month == 12 && $day >= 25) || ($month == 1 && $day <= 6);
	}

	public static function isValentines(): bool{
		return (date('n') == 2);
	}

	public static function isCortexsBirthday(): bool{
		$month = date('n');
		$day = date('j');

		return ($month == 10 && $day == 10);
	}

	public static function getRandomTSPSplash(): string{
		return self::$TEASPOON_SPLASHES[array_rand(self::$TEASPOON_SPLASHES)];
	}
}