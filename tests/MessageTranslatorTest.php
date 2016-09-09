<?php

namespace UserFrosting\I18n;

use PHPUnit\Framework\TestCase;

class MessageTranslatorTest extends TestCase
{

    public function testTranslateEN()
    {

        // Create the $translator object
		$translator = new MessageTranslator();

        // Add search paths for the test locale files relative to this file. We won't test setPaths with this test (yet)
        $translator->setPaths([dirname(__FILE__)."/locale"]);

        // Load the en_US locale files, no user locale
        $translator->loadLocaleFiles('en_US');

        // Test basic functionality (Colors)
        $this->assertEquals($translator->translate('COLOR'), "Color");
        $this->assertEquals($translator->translate('COLORS'), "Colors");

        $this->assertEquals($translator->translate('COLOR', 0), "colors"); //Note plural in english, singular in french !
        $this->assertEquals($translator->translate('COLOR', 1), "color");
        $this->assertEquals($translator->translate('COLOR', 2), "colors");
        $this->assertEquals($translator->translate('COLOR', 3), "colors");

        $this->assertEquals($translator->translate('COLOR.BLACK'), "black");
        $this->assertEquals($translator->translate('COLOR.WHITE'), "white");

        // Test placeholders
        $this->assertEquals($translator->translate('MY_CAR_MAKE', ["car_make" => "Toyota"]), "My car is a Toyota");
        $this->assertEquals($translator->translate('MY_CAR_YEAR', ["year" => 2015]), "I bought my car in 2015");

        // Test plural called without a plural value
        $this->assertEquals($translator->translate('X_CARS'), "X_CARS");

        // Test plural placeholder
        $this->assertEquals($translator->translate('X_CARS', 0), "no cars");
        $this->assertEquals($translator->translate('X_CARS', 1), "a car");
        $this->assertEquals($translator->translate('X_CARS', 2), "2 cars");
        $this->assertEquals($translator->translate('X_CARS', 10), "10 cars");

        // Example of a lang key in a placeholder
        $this->assertEquals($translator->translate('MY_CARS', ["x_cars" => $translator->translate('X_CARS', 10)]), "I have 10 cars");

        // Test `+CAR_TYPE` called (top nested name) without "CAR_TYPE" defined
        $this->assertEquals($translator->translate('CAR_TYPE'), "CAR_TYPE");

        // Test 3 levels nested with "CAR_TYPE"
        $this->assertEquals($translator->translate('CAR_TYPE.GAS'), "gas");
        $this->assertEquals($translator->translate('CAR_TYPE.EV'), "electric");
        $this->assertEquals($translator->translate('CAR_TYPE.EV.HYBRID'), "hybrid");
        $this->assertEquals($translator->translate('CAR_TYPE.HYDROGEN'), "hydrogen");

        // Test extra placeholder (`year` not used)
        $this->assertEquals($translator->translate("MY_CAR_MAKE", ["car_make" => "Toyota", "year" => 2014]), "My car is a Toyota");

        // Test missing placeholder (`car_make` nor defined)
        $this->assertEquals($translator->translate("MY_CAR_MAKE"), "My car is a {{car_make}}");

        // Example of a complex translation
        $this->assertEquals($translator->translate('MY_CAR_STRING', [
            "my_car" => $translator->translate('CAR_TYPE.EV.PLUGIN_HYBRID'),
            "color" => $translator->translate('COLOR.RED')
        ]), "I drive a red plug-in hybrid");

        // Test `plural` pluralisation placeholder with other placeholders
        $this->assertEquals($translator->translate("MY_EV_CARS", [
            "plural" => 3,
            "car_type" => $translator->translate("CAR_TYPE.EV", 3)
        ]), "I have 3 electric cars");

         // Test pluralisation with custom plural key
        $this->assertEquals($translator->translate("HUNGRY_CATS", ["num" => 0], "num"), "0 hungry cats");
        $this->assertEquals($translator->translate("HUNGRY_CATS", ["num" => 1], "num"), "1 hungry cat");
        $this->assertEquals($translator->translate("HUNGRY_CATS", ["num" => 2], "num"), "2 hungry cats");
        $this->assertEquals($translator->translate("HUNGRY_CATS", ["num" => 5], "num"), "5 hungry cats");

        // Custom key can also be omited in the placeholder if it's the only placeholder even with custom plural key
        $this->assertEquals($translator->translate("HUNGRY_CATS", 5, "num"), "5 hungry cats");

        // Example of expected error because of the custom plural key
        $this->assertEquals($translator->translate("HUNGRY_CATS", 0), "{{num}} hungry cats");
        $this->assertEquals($translator->translate("HUNGRY_CATS", 1), "{{num}} hungry cat");
        $this->assertEquals($translator->translate("HUNGRY_CATS", 2), "{{num}} hungry cats");
        $this->assertEquals($translator->translate("HUNGRY_CATS", 5), "{{num}} hungry cats");

        // Example : Custom key needs to be in the 3rd argument! It won't even find `@HUNGRY_CATS` without a valid plural value.
        $this->assertEquals($translator->translate("HUNGRY_CATS", ["num" => 5]), "HUNGRY_CATS");

        // Test missing pluralisation
        $this->assertEquals($translator->translate("HUNGRY_CATS"), "HUNGRY_CATS");

        // Test basic placeholder remplacement using int as placeholder value (So they don't try to translate "min" and "max")
        // We don't want to end up with "Votre test doit être entre minimum et 200 patates"
        $this->assertEquals($translator->translate("TEST_LIMIT", ["min" => 4, "max" => 200]), "Your test must be between 4 and 200 potatoes.");
    }

    public function testTranslateFR()
    {

        // Create the $translator object
		$translator = new MessageTranslator();

        // Add search paths for the test locale files relative to this file. We won't test setPaths with this test (yet)
        $translator->setPaths([dirname(__FILE__)."/locale"]);

        // Load the en_US locale files, no user locale
        $translator->loadLocaleFiles('fr_FR');

        // Test basic functionality (Colors)
        $this->assertEquals($translator->translate('COLOR'), "Couleur");
        $this->assertEquals($translator->translate('COLORS'), "Couleurs");

        $this->assertEquals($translator->translate('COLOR', 0), "couleur"); //Note plural in english, singular in french !
        $this->assertEquals($translator->translate('COLOR', 1), "couleur");
        $this->assertEquals($translator->translate('COLOR', 2), "couleurs");
        $this->assertEquals($translator->translate('COLOR', 3), "couleurs");

        $this->assertEquals($translator->translate('COLOR.BLACK'), "noir");
        $this->assertEquals($translator->translate('COLOR.WHITE'), "blanc");

        // Test placeholders
        $this->assertEquals($translator->translate('MY_CAR_MAKE', ["car_make" => "Toyota"]), "Ma voiture est une Toyota");
        $this->assertEquals($translator->translate('MY_CAR_YEAR', ["year" => 2015]), "J'ai acheté ma voiture en 2015");

        // Test plural called without a plural value
        $this->assertEquals($translator->translate('X_CARS'), "X_CARS");

        // Test plural placeholder
        $this->assertEquals($translator->translate('X_CARS', 0), "aucune voiture");
        $this->assertEquals($translator->translate('X_CARS', 1), "une voiture");
        $this->assertEquals($translator->translate('X_CARS', 2), "2 voitures");
        $this->assertEquals($translator->translate('X_CARS', 10), "10 voitures");

        // Example of a lang key in a placeholder
        $this->assertEquals($translator->translate('MY_CARS', ["x_cars" => $translator->translate('X_CARS', 10)]), "J'ai 10 voitures");

        // Test `+CAR_TYPE` called (top nested name) without "CAR_TYPE" defined
        $this->assertEquals($translator->translate('CAR_TYPE'), "CAR_TYPE");

        // Test 3 levels nested with "CAR_TYPE"
        $this->assertEquals($translator->translate('CAR_TYPE.GAS'), "à essence");
        $this->assertEquals($translator->translate('CAR_TYPE.EV'), "électrique");
        $this->assertEquals($translator->translate('CAR_TYPE.EV.HYBRID'), "hybride");
        $this->assertEquals($translator->translate('CAR_TYPE.HYDROGEN'), "à l'hydrogène");

        // Test extra placeholder (`year` not used)
        $this->assertEquals($translator->translate("MY_CAR_MAKE", ["car_make" => "Toyota", "year" => 2014]), "Ma voiture est une Toyota");

        // Test missing placeholder (`car_make` nor defined)
        $this->assertEquals($translator->translate("MY_CAR_MAKE"), "Ma voiture est une {{car_make}}");

        // Example of a complex translation
        $this->assertEquals($translator->translate('MY_CAR_STRING', [
            "my_car" => $translator->translate('CAR_TYPE.EV.PLUGIN_HYBRID'),
            "color" => $translator->translate('COLOR.RED')
        ]), "Je conduit une hybride branchable de couleur rouge");

        // Test `plural` pluralisation placeholder with other placeholders
        $this->assertEquals($translator->translate("MY_EV_CARS", [
            "plural" => 3,
            "car_type" => $translator->translate("CAR_TYPE.EV", 3)
        ]), "J'ai 3 voitures électriques");

         // Test pluralisation with custom plural key
        $this->assertEquals($translator->translate("HUNGRY_CATS", ["num" => 0], "num"), "0 chat affamé");
        $this->assertEquals($translator->translate("HUNGRY_CATS", ["num" => 1], "num"), "1 chat affamé");
        $this->assertEquals($translator->translate("HUNGRY_CATS", ["num" => 2], "num"), "2 chats affamés");
        $this->assertEquals($translator->translate("HUNGRY_CATS", ["num" => 5], "num"), "5 chats affamés");

        // Custom key can also be omited in the placeholder if it's the only placeholder even with custom plural key
        $this->assertEquals($translator->translate("HUNGRY_CATS", 5, "num"), "5 chats affamés");

        // Example of expected error because of the custom plural key
        $this->assertEquals($translator->translate("HUNGRY_CATS", 0), "{{num}} chat affamé");
        $this->assertEquals($translator->translate("HUNGRY_CATS", 1), "{{num}} chat affamé");
        $this->assertEquals($translator->translate("HUNGRY_CATS", 2), "{{num}} chats affamés");
        $this->assertEquals($translator->translate("HUNGRY_CATS", 5), "{{num}} chats affamés");

        // Example : Custom key needs to be in the 3rd argument! It won't even find `@HUNGRY_CATS` without a valid plural value.
        $this->assertEquals($translator->translate("HUNGRY_CATS", ["num" => 5]), "HUNGRY_CATS");

        // Test missing pluralisation
        $this->assertEquals($translator->translate("HUNGRY_CATS"), "HUNGRY_CATS");

        // Test basic placeholder remplacement using int as placeholder value (So they don't try to translate "min" and "max")
        // We don't want to end up with "Votre test doit être entre minimum et 200 patates"
        $this->assertEquals($translator->translate("TEST_LIMIT", ["min" => 4, "max" => 200]), "Votre test doit être entre 4 et 200 patates.");
    }
}