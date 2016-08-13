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
        $this->assertEquals($translator->translate('THE_BEACH'), "the beach");

        // Test sub keys using dot syntax
        $this->assertEquals($translator->translate('COLOR_ARRAY.BLACK'), "black");

        // Test when specifying a master key containing subkeys (Expected error, because COLOR_ARRAY doesn't define a key)
        $this->assertEquals($translator->translate('COLOR_ARRAY'), "COLOR_ARRAY");

        // Test basic placeholder replacement
        $this->assertEquals($translator->translate("ME_IS", ["place" => "the beach"]), "I'm on the beach");
        $this->assertEquals($translator->translate("NAME_IS", ["name" => "Bob", "place" => "the beach"]), "Bob is on the beach");

        // Test basic placeholder remplacement using int as placeholder value (So they don't try to translate "min" and "max")
        $this->assertEquals($translator->translate("TEST_LIMIT", ["min" => 4, "max" => 200]), "Your test must be between 4 and 200 potatoes.");

        // Test extra placeholder
        $this->assertEquals($translator->translate("ME_IS", ["place" => "the beach", "fruit" => "apple"]), "I'm on the beach");

        // Test missing placeholder
        $this->assertEquals($translator->translate("NAME_IS", ["place" => "the beach"]), "{{name}} is on the beach");

        // Test basic nested/var placeholders
        $place = $translator->translate("THE_BEACH");
        $this->assertEquals($translator->translate("ME_IS", ["place" => $place]), "I'm on the beach");

        // Test basic `plural` pluralisation
        $this->assertEquals($translator->translate("CHILD", ["plural" => 0]), "no children");
        $this->assertEquals($translator->translate("CHILD", ["plural" => 1]), "a child");
        $this->assertEquals($translator->translate("CHILD", ["plural" => 2]), "2 children");
        $this->assertEquals($translator->translate("CHILD", ["plural" => 5]), "5 children");

        // Test the plurialisation shortcut
        $this->assertEquals($translator->translate("CHILD", 0), "no children");
        $this->assertEquals($translator->translate("CHILD", 1), "a child");
        $this->assertEquals($translator->translate("CHILD", 2), "2 children");
        $this->assertEquals($translator->translate("CHILD", 5), "5 children");

        // Test missing pluralisation
        $this->assertEquals($translator->translate("CHILD"), "{{plural}} children");

        // Test custom plural key
        $this->assertEquals($translator->translate("NB_ADULT", ["nb_adult" => 2], "nb_adult"), "2 adults");

        // Test `plural` pluralisation placeholder with other placeholders
        $this->assertEquals($translator->translate("CAT_HERE", ["plural" => 3, "color" => "black"]), "There is 3 black cats here");
        $this->assertEquals($translator->translate("DOG_HERE", ["nb" => 3, "color" => "white"], "nb"), "There is 3 white dogs here");

        // Test complex translations
        $carModel = "Tesla Model S";
        $this->assertEquals($translator->translate("COMPLEX_STRING", [
        	"child" => 1,                            //PLURAL SHORTCUT
        	"adult" => ["NB_ADULT", 0, "nb_adult"],  //ADULT key with plural and custom plural_key
        	"color" => "COLOR_ARRAY.WHITE",          //Nested translation
        	"car" => $carModel                       //Classic string
        ]), "There's a child and no adults in the white Tesla Model S");

        $this->assertEquals($translator->translate("COMPLEX_STRING", [
        	"child" => 0,                               //PLURAL SHORTCUT
        	"adult" => ["NB_ADULT", 5, "nb_adult"],     //ADULT key with plural
        	"color" => "COLOR_ARRAY.RED",               //Nested translation
        	"car" => ["CAR_DATA.FULL_MODEL", ["constructor" => "Honda", "model" => "Civic", "year" => 1993]]
        ]), "There's no children and 5 adults in the red Honda Civic 1993");
    }
}