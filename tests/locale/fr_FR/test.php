<?php

return [
    "PLURAL_RULE" => 2, //Required to get the right rule. French is 2, english is 1

    "USERNAME" => 'Nom d\'utilisateur', //Note the espace `\` caracter here. Won't be displayed in the test

    // Colors
    "COLOR" => [
        0 => "couleur",
        1 => "couleur",
        2 => "couleurs"
    ],
    "+COLOR" => [
        "BLACK" => "noir",
        "RED" => "rouge",
        "WHITE" => "blanc"
    ],

    // Cars
    "CAR" => [
        1 => "voiture",
        2 => "voitures"
    ],
    "X_CARS" => [
        0 => "aucune voiture",
        1 => "une voiture",
        2 => "{{plural}} voitures"
    ],
    "+CAR_TYPE" => [
        "GAS" => "à essence",
        "EV" => [
            1 => "électrique",
            2 => "électriques" //Other way to pluralize for 1/2. This is NOT required in english!
        ],
        "+EV" => [
            "FULL" => "100% électrique",
            "HYBRID" => "hybride",
            "PLUGIN_HYBRID" => "hybride branchable"
        ],
        "HYDROGEN" => "à l'hydrogène"
    ],

    // Placeholder strings
    "MY_CAR_STRING" => "Je conduit une {{my_car}} de couleur {{color}}",
    "MY_CAR_MAKE" => "Ma voiture est une {{car_make}}",
    "MY_CAR_YEAR" => "J'ai acheté ma voiture en {{year}}",
    "MY_CARS" => "J'ai {{x_cars}}",

    // Plural with placeholder
    "MY_EV_CARS" => [
        1 => "J'ai une voiture {{car_type}}",
        2 => "J'ai {{plural}} voitures {{car_type}}"
    ],

    // Custom plural key with no "zero" case.
    // In english, "2" should be used when the plural value is zero. In french, "1" should be used
	"X_HUNGRY_CATS" => [
		1 => "{{num}} chat affamé",
		2 => "{{num}} chats affamés",
	],

	// Min/max placeholder where the
	"TEST_LIMIT" => "Votre test doit être entre {{min}} et {{max}} patates.",
	"MIN" => "minimum",
	//"MAX" => "maximum" //Leave disabled for tests

];
