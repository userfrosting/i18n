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
Sub keys can be defined in language files for easier navigation or variable integration.

```
return array(
	"ACCOUNT" => array(
		"SINGUP" => "Sign Up",
		"REGISTER" => "Register"
	)
);
```

Sub keys are called using _dot syntax_ : `$translator->translate('ACCOUNT.REGISTER')` will return `Register`

### Pluralization

The plural system allow for easy pluralization of strings. This whole system is based on Mozilla and phpBB works and plural rules (See : https://developer.mozilla.org/en-US/docs/Mozilla/Localization/Localization_and_Plurals). Plural rules are defined like this in the languages files, using the plural rules along with the `zero` special case:
```
"NEW_MESSAGE" => array(
	0 => "No new message",
	1 => "You have one new message",
	2 => "You have {{int}} new messages",
)
```

The correct plural form will be available in the `int` placeholder using `$translator->translate("NEW_MESSAGE", {int: 5})` or the shortcut method `$translator->translate("NEW_MESSAGE", 5)` if you don't have others placeholders. The default `int` key can be overwritten using a third option like this: `$translator->translate("NEW_MESSAGE", {nb: 5}, 'nb')`. This may be useful if you pass an existing array to the translate function.

If a localized string contain more than more plural, for example `1 guest and 4 friends currently online`, you can apply the plural rule to both `guest` and `friends` by nesting translation functions:
```
$online_guest => translate("ONLINE_GUEST", [guest => 1]);
$online_friend => translate("ONLINE_FRIEND", [guest => 1]);
translate("ONLINE_USERS", ["guest" => online_guest, "friend" => $online_friend]);
```

Where `ONLINE_USERS => "{{guest}} and {{friend}} currently online";`
