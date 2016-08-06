<?php

$lang = array();

$lang = array_merge($lang,array(
    "PLURAL_RULE" => 1,
	"ABOUT" => "About",
	"ACCOUNT" => array(
		"SINGUP" => "Sign Up",
		"REGISTER" => "Register"
	),
	"NEW_MESSAGE" => array(
		0 => "No new message",
		1 => "You have one new message",
		2 => "You have {{int}} new messages",
	),
	"FOO" => array(
		0 => "No foos",
		1 => "A foo",
		2 => "{{nb}} foos!!!",
	),
	"CAT_HERE" => array(
		0 => "There is no {{color}} cats here",
		1 => "There is a {{color}} cat here",
		2 => "There is {{int}} {{color}} cats here",
	),
	"DOG_HERE" => array(
		0 => "There is no {{color}} dogs here",
		1 => "There is a {{color}} dog here",
		2 => "There is {{nb}} {{color}} dogs here",
	),
	"ME_IS"     => "I'm on {{place}}",
	"NAME_IS"   => "{{name}} is on {{place}}",
	"THE_BEACH" => "the beach",

	"COMPLEX_STRING" => "There's {guest} and {friend} eating {fruit} near {place}"
));

return $lang;
