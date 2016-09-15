<?php

return [
    "COMPLEX_STRING" => [
        "@TRANSLATION" => "There's {{child}} and {{adult}} in the {{color}} {{car}}",
        "@REPLACE" => ["color", "car", "child", "adult"],
    ],
    "X_CHILD" => [
        "@PLURAL" => "nb_child",
    	0 => "no children",
    	1 => "a child",
    	2 => "{{plural}} children",
    ],
    "X_ADULT" => [
        "@PLURAL" => "nb_adult",
    	0 => "no adults",
    	1 => "an adult",
    	2 => "{{nb_adult}} adults",
    ],
    "CAR" => [
        "FULL_MODEL" => "{{make}} {{model}} {{year}}"
    ],

    "COMPLEX_STRING2" => [
        "@TRANSLATION" => "There's {{&X_CHILD}} and {{&X_ADULT}} in the {{color}} {{&CAR.FULL_MODEL}}",
        "@REPLACE" => ["color",],
    ]
];
