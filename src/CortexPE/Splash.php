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

	// THE RANDOM SPLASH GENERATOR!!!!!!

	// A lot of the Nouns and Verbs are from: http://nonsense.x2d.org/script.js
	// Some are removed due to how long they are, and some have vulgar / NSFW word(s).
	// But still, Thanks to the author of the script for most of the random splashes...

    /** @var string[] */
	private static $RANDOM_NOUN = [
		"The sky",
		"Everything and more",
		"The clear star that is yesterday",
		"Tomorrow",
		"An old apple",
		"Camouflage paint",
		"A sound you heard",
		"A setback of the heart",
		"The body of mind",
		"A classical composition",
		"Another day",
		"Chair number eleven",
		"Nihilism",
		"Tranquility",
		"Wondrous awe",
		"That memory we used to share",
		"Nothing of importance",
		"Clear water",
		"Gasoline",
		"Sixty-four",
		"Nothingness",
		"The flow of quizzes",
		"An enigma",
		"Stupidity",
		"Love",
		"An idea",
		"The last sentence you saw",
		"The person you were before",
		"A flailing monkey",
		"Organisational culture",
		"Trickery",
		"A caring mother",
		"A sickingly prodigous profile",
		"A fly",
		"Two-finger John",
		"Sevenworm",
		"Pinocchio",
		"Lucky number slevin",
		"A shooting star",
		"Whiskey on the table",
		"A cranky old lady",
		"Stew and rum",
		"Spam",
		"Lonely Henry",
		"Style",
		"Fashion",
		"A principal idea",
		"Too long a stick",
		"A glittering gem",
		"That way",
		"Significant understanding",
		"Passion or serendipity",
		"A late night",
		"A stumbling first step",
		"That stolen figurine",
		"A token of gratitude",
		"A small mercy",
		"Utter nonsense",
		"Colorful clay",
		"Insignificance",
		"The light at the end of the tunnel",
		"The other side",
		"Abstraction",
		"Rock music",
		"A passionate evening",
		"A great silence",
		"A river a thousand paces wide",
		"The legend of the raven's roar",
		"Enqoyism",
		"The Audacity",
		"da wae",
	];

	/** @var string[] */
	private static $RANDOM_VERB = [
		"runs through everything.",
		"is ever present.",
		"approaches at high velocity!",
		"likes to take a walk in the park.",
		"is still not very coherent.",
		"loves to love.",
		"would die for a grapefruit!",
		"sickens me.",
		"has your skin crawling.",
		"makes people shiver.",
		"is always a pleasure.",
		"slips on a banana peel.",
		"is nothing at all?",
		"doesn't like paying taxes.",
		"is not yet ready to die.",
		"is omni-present, much like candy.",
		"is good for you.",
		"does not make any sense.",
		"would scare any linguist away.",
		"sees the sun.",
		"is running away.",
		"jumps both ways.",
		"can get both high and low.",
		"comes asking for bread.",
		"says hello.",
		"tenderly sees to her child.",
		"wants to go to hell.",
		"is often pregnant.",
		"is often one floor above you.",
		"wants to set things right.",
		"tells the tale of towers.",
		"stole the goods.",
		"woke the prime minister.",
		"shot the sheriff.",
		"lay down on the riverbed.",
		"asked you a question?",
		"sat down once more.",
		"revels in authority.",
		"stands upon somebody else's legs.",
		"visits Japan in the winter.",
		"says goodbye to the shooter.",
		"welcomes spring!",
		"loves a good joke!",
		"is a storyteller without equal.",
		"rains heavily.",
		"is like a summer breeze.",
		"wanted the TRUTH!",
		"set a treehouse on fire.",
		"bathes in sunlight.",
		"ever stuns the onlooker.",
		"brings both pleasure and pain.",
		"takes the world for granted.",
		"is not enough.",
		"was always the second best.",
		"is not all that great.",
		"shakes beliefs widely held.",
		"always strikes for the heart.",
		"crawls the interwebs",
	];

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
		"I'm sorry Dave. I'm afraid I can't do that.",
		"I might have killed it.",
		"We have VCS Systems. :P",
		"We have *crappy* VCS Systems. :P",
		":shrug:",
		"Fukkit!",
		"§4R§cA§6I§eN§2B§aO§bW§3 T§1E§9X§dT§5!",
		"@TheAz928 is notorious for HardCoding values!",


		// SoftwareGore from: Best of r/SoftwareGore -- https://www.youtube.com/watch?v=kekn2HhE-qI  *I'M DYING*
		"DAMMIT STEVE",
		"Best of r/SoftwareGore",
		"*Music Plays*",
		"Installing Dragon Center Update 147%",
		"The Power Saver app may drain the battery.",
		"YO BRO THAT'S A Cool Sign!!! TOTES LIT AF RELATABLE :joy: AMIRITE???",
		":( Your PC ran and We're jus... For more anforma... If you call suppor... DRI",
		"Could not complete your request because Brendan's an idiot.",
		"CONGRATULATIONS YOU GOT THEM ALL WRONG!!!",
		"SHAKESPEARE QUOTE OF THE DAY: An SSL error has occured and a secure connection to the server cannot be made.",
		"It is a very chilly -3602°F, I wouldn't recommend going outside because you may actually freeze to death.",
		"Rest In Peace Me, Goodbye World.",
		"Russia is located in Russia",
		"Do you want to change your default web browser to \"Chrome\" or keep using \"Chrome\"?",
		"What do you identify as? Correct Answer: Female",
		":( Your PC ra We're (0% Complete)",
		"If you would like to k KMODE_EXCEPTION_NOT_HANDLED",
		"You need to be logged in to log out. Please log in to log out.",
		"We all know there are nine genders.",
		"F",
		"M",
		"Male",
		"Female",
		"Famale",
		"Felmale",
		"High School visit on March 17",
		"Gender",
		"International High School Visit at Ho Chi Minh City at March 19",
		"Do you really want to exist without saving?",
		"Something Happened. SOMETHING HAPPENED!!!!!",
		"??? ???",
		"OK",
		"This Driver can't",
		"Great! That's what we're all about here at the ZPD!",
		"please don't",
		"Amazing Russian Bombshells Want To Date You!",
		":( Your P",
		"Tip of the Day: Chc xnt j mnv ---",
		"ok",
		"Java Update???? Java??????????????????????",
		"Seriously I get to have my own undefined? THIS IS THE BEST DAY EVER",
		"IUWFHIURGREIOGHERIGUIORGHELGTHEKJFGHIKDFGIU",
		"Please wait while OneNote inserts the d...",
		":( Yo",
		"His code is weak",
		"OS Unsteady",
		"Garbage in his collector already",
		"CD-ROM Spagetti",
		"Which direction is North? It's Rob Reiner.",
		"Windows will shut dosistant will reboot yote",
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
		"First comes... LOVE Then comes... MORRIAGE Then comes a... BOBY",
		"Non ACTION and Stop EXCITEMENT",
		"Nesquick from the Nesdi**!",
		"THE CUMMY",
		"NOW HIRING NOW RIGHT NOW WE'RE HIRING NOW",
		"BLONK",
		"Stairs & Elevators & Terminal & Stairs & Elevators & Terminal & Stairs",
		"COTTON CHICKEN CANDY NUGGETS",
		"FIND A COLON NEAR YOU",
		"It's NOT Its ME YOU",
		"DO NOT BRING FOOD OR DRINK IN LAB - STOP - NO - FOOD OR DRINK - ALLOWED - IN LAB",
		"PAIN REGRET COURAGE RICE",
		"I Give Up",

		// Base64 Encoded strings
		"R09UIEVNIQ==",
		"R0VUIFBSQU5LRUQgQlJPISEh",
		"b09vIEJhc2U2NCBvT28=",
		"cnVubmluZyBvdXR0YSBpZGVhcyBsb2w=",
		"b2ggbWFoIGdhaA==",
	];

	/** @var string[] */
	private static $CHRISTMAS_SPLASHES = [
		"Ho Ho Ho...",
		"Merry Christmas",
	];

	public static function getRandomSplash(): string{
		if(self::isWednesday() && mt_rand(1,2) == 1){
			return "It's WEDNESDAY my dudes.";
		}
		if(self::isChristmastide()){
			return self::$CHRISTMAS_SPLASHES[array_rand(self::$CHRISTMAS_SPLASHES)];
		}
		if(self::isValentines() && mt_rand(1,2) == 2){
			return self::VALENTINES_SPLASH;
		}
		if(self::isCortexsBirthday()){
			return (mt_rand(1, 2) == 1 ? "Cortex's biological age is now " . strval(intval(date('Y')) - 1999) . "!" : "Happy birthday Cortex!"); // lolz
		}
		if(mt_rand(0, 100) <= mt_rand(75, 100)){
			while(true){
				$rand = self::getRandomSentence();
				$len = strlen($rand);
				if($len <= 32){ // max length 32
					return $rand;
				}
			}
		}

		return self::getRandomTSPSplash();
	}

	public static function isChristmastide() : bool {
		$month = date('n');
		$day = date('j');

		return ($month == 12 && $day >= 25) || ($month == 1 && $day <= 6);
	}

	public static function isCortexsBirthday() : bool {
		$month = date('n');
		$day = date('j');

		return ($month == 10 && $day == 10);
	}

	public static function isValentines() : bool {
		return (date('n') == 2);
	}

	public static function isWednesday() : bool {
		return (date('w') == 3);
	}

	public static function getRandomSentence(): string{
		return self::getRandomNoun() . " " . self::getRandomVerb();
	}

	public static function getRandomNoun(): string{
		return self::$RANDOM_NOUN[array_rand(self::$RANDOM_NOUN)];
	}

	public static function getRandomVerb(): string{
		return self::$RANDOM_VERB[array_rand(self::$RANDOM_VERB)];
	}

	public static function getRandomTSPSplash(): string{
		return self::$TEASPOON_SPLASHES[array_rand(self::$TEASPOON_SPLASHES)];
	}
}