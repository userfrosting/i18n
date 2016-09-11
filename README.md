# I18n module for UserFrosting

Alexander Weissman, 2016

The I18n module handles translation tasks for UserFrosting.  The `MessageTranslator` class can be used as follows:

## Basic usage
### Step 1 - Set up language file(s).

A language file returns an array mapping message tokens to localized messages.  Messages may optionally have placeholders.  For example:

**locale/es_ES/main.php**

```
return array(
	"ACCOUNT_SPECIFY_USERNAME" => "Introduce tu nombre de usuario.",
	"ACCOUNT_SPECIFY_DISPLAY_NAME" => "Introduce tu nombre público.",
	"ACCOUNT_USER_CHAR_LIMIT" => "Tu nombre de usuario debe estar entre {{min}} y {{max}} caracteres de longitud."
);
```

**locale/en_US/main.php**

```
return array(
	"ACCOUNT_SPECIFY_USERNAME" => "Please enter your user name.",
	"ACCOUNT_SPECIFY_DISPLAY_NAME" => "Please enter your display name.",
	"ACCOUNT_USER_CHAR_LIMIT" => "Your user name must be between {{min}} and {{max}} characters in length."
);
```

### Step 2 - Set up translator object

```
$translator = new \UserFrosting\I18n\MessageTranslator();
$translator->setPaths("locale/");
$translator->loadLocaleFiles("en_US");
```

The above will load the files located in the `locale/en_US` directory. You can also define a second language in `loadLocaleFiles`. This second language will be loaded on top of the other. For example:

```
$translator->loadLocaleFiles("en_US", "es_ES");
```

This will use the `english` translation as a base and load the `spanish` translation on top. All keys not found in the spanish translation will fallback to the english one.

### Step 3 - Do a translation!

```
echo $translator->translate("ACCOUNT_USER_CHAR_LIMIT", [
    "min" => 4,
    "max" => 200
]);

// Returns "Tu nombre de usuario debe estar entre 4 y 200 caracteres de longitud."
```
## Advanced usage

### Sub keys
Sub keys can be defined in language files for easier navigation of lists or to distinguish two items with common keys. They are identified by the `+` prefix. This prefix allow both standard key and sub key to coexist. For example:

```
return [
  "COLOR"  => "Color",
  "+COLOR" => [
    "BLACK" => "black",
    "RED" => "red",
    "WHITE" => "white"
  ]
];
```

Sub keys can be accessed using _dot syntax_. Note that the `+` prefix will automatically be added. So `$translator->translate('COLOR.BLACK')` will return `black` while `$translator->translate('COLOR')` will return `Color`.

**WARNING** : Omitting the `+` prefix in the languages files will result in unexpected behavior and throw errors!

Sub keys are really useful when multiple *master keys* share the same sub keys:
```
return [
	"+METHOD_A" => [
		"TITLE" => "Scénario A",
		"DESCRIPTION" => "..."
	],
	"+METHOD_B" => [
		"TITLE" => "Scénario B",
		"DESCRIPTION" => "..."
	]
];

$method = Method->get(); // return $method = "METHOD_A";
echo $translator->translate("$method.TITLE"); // Print "Scénario A"
```


### Pluralization

The plural system allow for easy pluralization of strings. This whole system is based on Mozilla plural rules (See : https://developer.mozilla.org/en-US/docs/Mozilla/Localization/Localization_and_Plurals). For a given language, there is a grammatical rule on how to change words depending on the number qualifying the word. Different languages can have different rules. For example, in English you say `no cars` (note the plural `cars`) while in French you say `Aucune voiture` (note the singular `voiture`).

The rule associated with a particular language ([see link above](https://developer.mozilla.org/en-US/docs/Mozilla/Localization/Localization_and_Plurals))) is defined in the `PLURAL_RULE` key. So in the `english` file, you should find `"PLURAL_RULE" => 1` and in the `french` file `"PLURAL_RULE" => 2`.

Strings with plural forms are defined as sub arrays with the rules as the key. The right plural form is determined by the plural value passed as the second parameter of the `translate` function :
```
"HUNGRY_CATS" => [
	0 => "hungry cats",
	1 => "hungry cat",
	2 => "hungry cats",
]

echo translate("HUNGRY_CATS", 0); // Return "hungry cats"
echo translate("HUNGRY_CATS", 1); // Return "hungry cat"
echo translate("HUNGRY_CATS", 2); // Return "hungry cats"
echo translate("HUNGRY_CATS", 5); // Return "hungry cats"
```

The plural value used to select the right form is defined by default in the `plural` placeholder. This means that `$translator->translate("HUNGRY_CATS", 5)` is equivalent to `$translator->translate("HUNGRY_CATS", ['plural' => 5])`. The `plural` placeholder can also be used in the string definition. In this case, the `X_` prefix for the key is prefered to mark the plural will be displayed:

```
"X_HUNGRY_CATS" => [
	0 => "No hungry cats",
	1 => "{{plural}} hungry cat",
	2 => "{{plural}} hungry cats",
]

echo translate("X_HUNGRY_CATS", 0); // Return "No hungry cats"
echo translate("X_HUNGRY_CATS", 1); // Return "1 hungry cat"
echo translate("X_HUNGRY_CATS", 2); // Return "2 hungry cats"
echo translate("X_HUNGRY_CATS", 5); // Return "5 hungry cats"
echo translate("X_HUNGRY_CATS", ['plural': 5]); // Return "5 hungry cats" (equivalent to the previous one)
```

In this example, `0` is used as a special rules to display `No hungry cats` instead of `0 hungry cats` for prettier strings.

When the first argument of the `translate` function points to a plural key in the language definition files and the second parameter is omitted, the plural value will be `1` by default. That means `translate("X_HUNGRY_CATS", 1)` is equivalent to `translate("X_HUNGRY_CATS")`.


#### Custom plural key
The default `plural` key can be overwritten by passing a third (optional) argument to the `translate` function. This may be useful if you pass an existing array to the translate function.

```
"NB_HUNGRY_CATS" => [
	0 => "No hungry cats",
	1 => "One hungry cat",
	2 => "{{nb}} hungry cats",
]

echo translate("NB_HUNGRY_CATS", 2, "nb"); // Return "2 hungry cats"
echo translate("NB_HUNGRY_CATS", ['nb': 5], "nb"); // Return "5 hungry cats"
```

#### Plural value with placeholders
If you have more than one placeholder, you must then pass the plural value in the placeholders (no shortcut possible).

```
"X_EMOTION_CATS" => [
 0 => "No {{emotion}} cats",
 1 => "One {{emotion}} cat",
 2 => "{{plural}} {{emotion}} cats",
]

echo translate("X_EMOTION_CATS", ['plural': 2, 'emotion': 'hungry']); // Return "2 hungry cats"
echo translate("X_EMOTION_CATS", ['plural': 5, 'emotion': 'angry']); // Return "5 angry cats"
```

#### Multiple plural in a string
If a localized string contain more than more plural, for example `1 guest and 4 friends currently online`, you can apply the plural rule to both `guest` and `friends` by nesting the `ONLINE_GUEST` and `ONLINE_FRIEND` keys into `ONLINE_USERS`:
```
"ONLINE_GUEST" => [
	0 => "0 guests",
	1 => "1 guest",
	2 => "{{plural}} guests"
],

"ONLINE_FRIEND" => [
	0 => "0 friends",
	1 => "1 friend",
	2 => "{{plural}} friends"
],

"ONLINE_USERS" => "{{guest}} and {{friend}} currently online",

[...]

$online_guest => translate("ONLINE_GUEST", 1);
$online_friend => translate("ONLINE_FRIEND", 4);
translate("ONLINE_USERS", ["guest" => $online_guest, "friend" => $online_friend]); // Returns "1 guest and 4 friends currently online"
```

Note that nested translations can be used when faced with long sentence using multiples sub strings or plural form, but those should be avoided when possible. Shorter or multiple sentences should be preferred instead.

#### Numbers are rules, not limits !
**REALLY IMPORTANT** : The **number** defined in the language files **IS NOT** related to the plural value, but to [the plural rule](https://developer.mozilla.org/en-US/docs/Mozilla/Localization/Localization_and_Plurals). **So this is completely WRONG** :

```
"X_HUNGRY_CATS" => [
	0 => "No hungry cats",
	1 => "One hungry cat",
	2 => "{{plural}} hungry cats",
	5 => "A lot of hungry cats"
]

echo translate("X_HUNGRY_CATS", 2); // Return "2 hungry cats"
echo translate("X_HUNGRY_CATS", 5); // Return "5 hungry cats", NOT "A lot of hungry cats"!
```

#### One last thing...
In some cases, it could be faster and easier to directly access the plural value. For example, when the string will *always* be plural. Consider the following example :
```
"COLOR" => [
  0 => "colors",
  1 => "color",
  2 => "colors"
],
"COLORS" => "Colors",
```
In this example, `translate("COLOR", 2);` and `translate("COLORS");` will return the same value. This is true for English, but not necessarily for all languages. While languages without any form of plural definitions could define `"COLOR" => "Color"` and `"COLORS" => "Color"`, some may have even more complicated rules. That's why it's always best to avoid keys like `COLORS` if you plan to translate to more than one language. This is also true with the `0` value that can be different across different language, but can also be handle differently depending of the message you want to display (Ex.: `No colors` instead of `0 colors`).


## One last example...

### Language file
```
"COMPLEX_STRING" => "There's {{child}} and {{adult}} in the {{color}} {{car}}",
"X_CHILD" => [
	0 => "no children",
	1 => "a child",
	2 => "{{plural}} children",
],
"X_ADULT" => [
	0 => "no adults",
	1 => "an adult",
	2 => "{{nb_adult}} adults",
],
"+COLOR" => [
	"WHITE" => "white",
	"BLACK" => "black",
	"RED" => "red"
],
"+CAR"  => [
  	"FULL_MODEL" => "{{make}} {{model}} {{year}}"
]
```

### translate function
```
$carMake = "Honda";
echo $translator->translate("COMPLEX_STRING", [
	"child" => $translator->translate("X_CHILD", 1),
	"adult" => $translator->translate("NB_ADULT", 0, "nb_adult")
	"color" => $translator->translate("COLOR.WHITE")
	"car" => $translator->translate("CAR.FULL_MODEL", ["make" => $carMake, "model" => "Civic", "year" => 1993])
]);
```

### Result
```
There's a child and no adults in the white Honda
```
