<?php

$lang = array();

$lang = array_merge($lang,array(
    "PLURAL_RULE" => 1,

	"CAT_HERE" => array(
		0 => "There is no {{color}} cats here",
		1 => "There is a {{color}} cat here",
		2 => "There is {{plural}} {{color}} cats here",
	),
	"DOG_HERE" => array(
		0 => "There is no {{color}} dogs here",
		1 => "There is a {{color}} dog here",
		2 => "There is {{nb}} {{color}} dogs here",
	),
	"ME_IS"     => "I'm on {{place}}",
	"NAME_IS"   => "{{name}} is on {{place}}",
	"THE_BEACH" => "the beach",

	"TEST_LIMIT" => "Your test must be between {{min}} and {{max}} potatoes.",
	"MIN" => "minimum",
	//"MAX" => "maximum" //Leave disabled for tests

	"COMPLEX_STRING" => "There's {{child}} and {{adult}} in the {{color}} {{car}}",
	"CHILD" => array(
		0 => "no children",
		1 => "a child",
		2 => "{{plural}} children",
	),
	"NB_ADULT" => array(
		0 => "no adults",
		1 => "an adult",
		2 => "{{nb_adult}} adults",
	),
	"COLOR_ARRAY" => array(
		"WHITE" => "white",
		"BLACK" => "black",
		"RED" => "red",
	),
	"CAR_DATA"  => array(
    	"FULL_MODEL" => "{{constructor}} {{model}} {{year}}"
	)
));

return $lang;
