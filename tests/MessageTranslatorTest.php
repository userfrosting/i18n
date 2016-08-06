<?php

namespace UserFrosting\I18n;

use PHPUnit\Framework\TestCase;

class MessageTranslatorTest extends TestCase
{

    public function testTranslate()
    {

        // Create the $translator object
		$translator = new MessageTranslator();

        // Add search paths for the test locale files relative to this file. We won't test setPaths with this test (yet)
        $translator->setPaths([dirname(__FILE__)."/locale"]);

        // Load the en_US locale files, no user locale
        $translator->loadLocaleFiles('en_US');

        // Test basic functionality
        $this->assertEquals($translator->translate('ABOUT'), "About");

        // Test sub keys using dot syntax
        $this->assertEquals($translator->translate('ACCOUNT.REGISTER'), "Register");

        // Test basic placeholder resplacement
        $this->assertEquals($translator->translate("ME_IS", ["place" => "the beach"]), "I'm on the beach");
        $this->assertEquals($translator->translate("NAME_IS", ["name" => "Bob", "place" => "the beach"]), "Bob is on the beach");

        // Test extra placeholder
        $this->assertEquals($translator->translate("ME_IS", ["place" => "the beach", "fruit" => "apple"]), "I'm on the beach");

        // Test missing placeholder
        $this->assertEquals($translator->translate("NAME_IS", ["place" => "the beach"]), "{{name}} is on the beach");

        // Test nested placeholders
        $place = $translator->translate("THE_BEACH");
        $this->assertEquals($translator->translate("ME_IS", ["place" => $place]), "I'm on the beach");

        // Test basic int pluralisation
        $this->assertEquals($translator->translate("NEW_MESSAGE", ["int" => 0]), "No new message");
        $this->assertEquals($translator->translate("NEW_MESSAGE", ["int" => 1]), "You have one new message");
        $this->assertEquals($translator->translate("NEW_MESSAGE", ["int" => 2]), "You have 2 new messages");
        $this->assertEquals($translator->translate("NEW_MESSAGE", ["int" => 5]), "You have 5 new messages");

        // Test the plurialisation shortcut
        $this->assertEquals($translator->translate("NEW_MESSAGE", 0), "No new message");
        $this->assertEquals($translator->translate("NEW_MESSAGE", 1), "You have one new message");
        $this->assertEquals($translator->translate("NEW_MESSAGE", 2), "You have 2 new messages");
        $this->assertEquals($translator->translate("NEW_MESSAGE", 5), "You have 5 new messages");

        // Test missing pluralisation
        $this->assertEquals($translator->translate("NEW_MESSAGE"), "You have {{int}} new messages");

        // Test custom plural key
        $this->assertEquals($translator->translate("FOO", ["nb" => 2], "nb"), "2 foos!!!");

        // Test int pluralisation with other placeholders
        $this->assertEquals($translator->translate("CAT_HERE", ["int" => 3, "color" => "black"]), "There is 3 black cats here");
        $this->assertEquals($translator->translate("DOG_HERE", ["nb" => 3, "color" => "white"], "nb"), "There is 3 white dogs here");

        //!TODO:
        /*
            translate("MAIN_STRING", {
            	"guest" => {"GUEST_STRING", 1},
            	"friend" => {"FRIEND_STRING", 4},
            	"place" => "THE_BEACH",
            	"fruit" => {$selectedFruit, 4}
            })

            translate("MAIN_STRING", {
            	"guest" => {"HOBO", 1},
            	"friend" => {"DRUG_DEALER", 4},
            	"place" => "THE_ROOF",
            	"fruit" => {$meth, 4}
            })

            main string need to stay with the place holder. We could do a shortcut where if the place holder is a number, we use "placeholder_string" convention, an array, recursion and a String classic... Well recursion too I guess.
        */
    }
}