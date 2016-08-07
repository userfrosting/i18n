# I18n module for UserFrosting

Alexander Weissman, 2016

The I18n module handles translation tasks for UserFrosting.  The `MessageTranslator` class can be used as follows:

## Basic usage
### Step 1 - Set up language file(s).

A language file returns an array mapping message tokens to messages.  Messages may optionally have placeholders, plural form and sub messages.  For example:

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
Sub keys can be defined in language files for easier navigation of lists or to distinguish two items with common keys. For example:

```
return array(
	"COLOR" => array(
		"WHITE" => "white",
		"BLACK" => "black",
		"RED" => "red"
	)
);
```

Sub keys can be accessed using _dot syntax_. For example, `$translator->translate('COLOR.BLACK')` will return `black`.

Be careful when defining keys names in language files. In the above example, the `COLOR` key (`$translator->translate('COLOR')`) cound't be used to display the translated word `color`! The above example should be using the `ARRAY` suffix, for example `COLOR_ARRAY`.

Sub keys become rally useful if multiple *master keys* shared the same sub keys:
```
return array(
	"METHOD_A" => array(
		"TITLE" => "Scénario A",
		"DESCRIPTION" => "..."
	),
	"METHOD_B" => array(
		"TITLE" => "Scénario B",
		"DESCRIPTION" => "..."
	)
);

$method = Method->get(); // return $method = "A";
echo $translator->translate("METHOD_$method.TITLE"); // Print "Scénario A"
```



### Pluralization

The plural system allow for easy pluralization of strings. This whole system is based on Mozilla plural rules (See : https://developer.mozilla.org/en-US/docs/Mozilla/Localization/Localization_and_Plurals). The rule associated with a particular language (see link) is defined in the `PLURAL_RULE` key. So in the `english` file, you should find `"PLURAL_RULE" => 1` and in the `french` file `"PLURAL_RULE" => 2`.

Strings with plural forms are defined like this in the languages files, with the `zero` special case:
```
"NEW_MESSAGE" => array(
	0 => "No new message",
	1 => "You have one new message",
	2 => "You have {{int}} new messages",
)
```

The plural value used to select the right form is defined by default in the `int` placeholder. For example, `$translator->translate("NEW_MESSAGE", {int: 5})` or using the shortcut `$translator->translate("NEW_MESSAGE", 5)` if you don't have others placeholders. The default `int` key can be overwritten using a third option like this: `$translator->translate("NEW_MESSAGE", {nb: 5}, 'nb')`. This may be useful if you pass an existing array to the translate function.

If a localized string contain more than more plural, for example `1 guest and 4 friends currently online`, you can apply the plural rule to both `guest` and `friends` by nesting translation functions:
```
$online_guest => translate("ONLINE_GUEST", 1);
$online_friend => translate("ONLINE_FRIEND", 4);
translate("ONLINE_USERS", ["guest" => online_guest, "friend" => $online_friend]);
```

Where `ONLINE_USERS => "{{guest}} and {{friend}} currently online";`. You could also use *complex translation* (see below).

### Complex translations

Complex translations can be used when faced with long sentence using multiples sub strings or plural form. This works by recursively translating placeholders. Complex translations should be avoided when possible and shorter or multiple sentences should be preferred instead. To explain complex translations, let's start with an example:

*Language file*
```
"COMPLEX_STRING" => "There's {{child}} and {{adult}} in the {{color}} {{car}}",
"CHILD" => array(
	0 => "no children",
	1 => "a child",
	2 => "{{int}} children",
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
```

*translate function*
```
$carModel = "Honda";
echo $translator->translate("COMPLEX_STRING", [
	"child" => 1,                           //1° INT SHORTCUT
	"adult" => ["NB_ADULT", 0, "nb_adult"], //2° ADULT key with plural
	"color" => "COLOR_ARRAY.WHITE",         //3° Nested translation
	"car" => $carModel                      //4° Classic string
]);
```

*Result*
```
There's a child and no adults in the white Honda
```			

In this case:
1. The placeholder `child` in the `COMPLEX_STRING` will be replaced by the plural form of the `CHILD` key (since no other key is defined), using `1` as the plural value (double shortcut method).
1. The placeholder `adult` in the `COMPLEX_STRING` will be replaced by the plural form of the `NB_ADULT` key, using `0` as the plural value and `nb_adult` as the custom plural key (shortcut method + custom plural key).
1. The placeholder `color` in the `COMPLEX_STRING` will be replaced by the value of the `COLOR_ARRAY -> WHITE` sub key (sub key method).
1. The placeholder `car` in the `COMPLEX_STRING` will be replaced by the value of `$carModel` (Simple method).

Got it? Let's try this one:
```
$translator->translate("COMPLEX_STRING", [
	"child" => 0,
	"adult" => ["NB_ADULT", 5, "nb_adult"],
	"color" => "COLOR_ARRAY.RED",
	"car" => ["CAR_DATA.FULL_MODEL", ["constructor" => "Honda", "model" => "Civic", "year" => 1993]]
])
```

This will display `There's no children and 5 adults in the red Honda Civic 1993`. In this case, the `car` placeholder will be replaced using the `CAR_DATA.FULL_MODEL` key (sub key method) using 3 placeholders. Again, be careful here! The function will detect that `1993` is an integer and will try to pluralize `year`. Since the `year` key doesn't return an array of plurals, `1993` will be used as a classic placeholder. So again, be careful about the language key naming convention.

In the end, the above is equivalent to this:

```
$child = $translator->translate("CHILD", ["int" => 0]);
$adult = $translator->translate("NB_ADULT", ["nb_adult" => 0], "nb_adult");
$color = $translator->translate("COLOR_ARRAY.RED");
$carFullModel = $translator->translate("CAR_DATA.FULL_MODEL", [
	"constructor" => "Honda",
	"model" => "Civic",
	"year" => "1993"
]);

$translator->translate("COMPLEX_STRING", [
	"child" => $child,
	"adult" => $adult,
	"color" => $color,
	"car" => $carFullModel
])
```
