<?php

/*
 * UserFrosting i18n (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/i18n
 * @copyright Copyright (c) 2013-2019 Alexander Weissman, Louis Charette
 * @license   https://github.com/userfrosting/i18n/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\I18n;

use UserFrosting\Support\Repository\Repository;

/**
 * MessageTranslator Class.
 *
 * Translate message ids to a message in a specified language.
 *
 * @author    Louis Charette
 * @author    Alexander Weissman (https://alexanderweissman.com)
 */
class MessageTranslator extends Repository
{
    /**
     * @var Twig_Environment A Twig environment used to replace placeholders.
     */
    protected $twig;

    /**
     * @var string The default key that contains the pluralization code.
     */
    protected $defaultPluralKey = 'plural';

    /**
     * Create the translator.
     *
     * @param array $items
     */
    public function __construct(array $items = [])
    {
        $this->items = $items;

        $loader = new \Twig_Loader_Filesystem();
        $this->twig = new \Twig_Environment($loader);
    }

    /**
     * Translate the given message id into the currently configured language, substituting any placeholders that appear in the translated string.
     *
     * Return the $messageKey if not match is found
     *
     * @param string    $messageKey             The id of the message id to translate. can use dot notation for array
     * @param array|int $placeholders[optional] An optional hash of placeholder names => placeholder values to substitute.
     *
     * @return string The translated message.
     */
    public function translate($messageKey, $placeholders = [])
    {
        // If we didn't find a match, we simply apply the placeholders to $messageKey
        if (!$this->has($messageKey)) {
            $message = $messageKey;
        } else {
            // Get the message
            $message = $this->get($messageKey);

            /* If the message is an array, we have to go deeper because an array can countain some special handles:
                - @TRANSLATION
                - @REPLACE
                - @TRANSLATE
                - @PLURAL
            */
            if (is_array($message)) {
                // Is the message array countain any plural rules (keys that are int)
                if (!empty(array_filter(array_keys($message), 'is_int'))) {

                    // Now we can handle plurals using the @PLURAL to define the plural key. If it's not defined, we use the default one
                    $pluralKey = (isset($message['@PLURAL'])) ? $message['@PLURAL'] : $this->defaultPluralKey;

                    // We try get the plural value and default to `1` if none is found
                    // We also allow for a shortcut using the second argument as a numeric value for simple strings.
                    $pluralValue = (isset($placeholders[$pluralKey]) ? (int) $placeholders[$pluralKey] : (!is_array($placeholders) && is_numeric($placeholders) ? $placeholders : null));

                    // Stop for a sec... We don't have a plural value, but before defaut to 1, we check if there's any @TRANSLATION handle
                    if (is_null($pluralValue) && (!$this->has($messageKey.'.@TRANSLATION') || $this->get($messageKey.'.@TRANSLATION') == null)) {

                        //Default
                        $pluralValue = 1;
                    }

                    // If plural value is still null, we have found our message..!
                    if (is_null($pluralValue)) {
                        $message = $this->get($messageKey.'.@TRANSLATION');
                    } else {

                        // Ok great. Now we need the right plural form.
                        // N.B.: Plurals is based on phpBB and Mozilla work : https://developer.mozilla.org/en-US/docs/Mozilla/Localization/Localization_and_Plurals
                        $keyFound = false;

                        // 0 is handled differently. We use it so that "0 users" may be displayed as "No users".
                        if ($pluralValue == 0 && isset($message[0])) {
                            $keyFound = 0;
                        } else {
                            $usePluralForm = $this->getPluralForm($pluralValue);
                            if (isset($message[$usePluralForm])) {
                                // The key we need exists, so we use it.
                                $keyFound = $usePluralForm;
                            } else {
                                // If the key we need doesn't exist, we use the previous one.
                                $numbers = array_keys($message);
                                foreach ($numbers as $num) {
                                    if (is_int($num) && $num > $usePluralForm) {
                                        break;
                                    }
                                    $keyFound = $num;
                                }
                            }
                        }

                        // If no key was found, use the last entry (because it is mostly the plural form).
                        if ($keyFound === false) {
                            $numbers = array_keys($message);
                            $keyFound = end($numbers);
                        }

                        $message = $message[$keyFound];

                        // If we used the shortcut and $placeholders is a numeric value
                        // it must be passed back as an array for replacement in the main $message
                        if (is_numeric($placeholders) || empty($placeholders)) {
                            $placeholders = [$pluralKey => $pluralValue];
                        }
                    }

                    // @TRANSLATION => When $messageKey is an array, this key is used. To use this, we can't have a plural value
                } elseif ($this->has($messageKey.'.@TRANSLATION')) {
                    $message = $this->get($messageKey.'.@TRANSLATION');
                // If we don't have plural AND a @TRANSLATION, we can't translate any translation key, so we will simply apply the placeholders to $messageKey
                } else {
                    $message = $messageKey;
                }
            }
        }

        // Ok, now we have a $message and need to replace the placeholders
        // Make sure $placeholders is an array otherwise foreach will fail
        if (!is_array($placeholders)) {
            return $message;
        }

        // Interpolate translatable placeholders values. This allows to
        // pre-translate placeholder which value starts with the `&` caracter
        foreach ($placeholders as $name => $value) {
            //We don't allow nested placeholders. They will return errors on the next lines
            if (is_array($value)) {
                continue;
            }

            // We test if the placeholder value starts the "&" caracter.
            // That means we need to translate that placeholder value
            if (substr($value, 0, 1) === '&') {
                // Remove the current placeholder from the master $placeholder
                // array, otherwise we end up in an infinite loop
                $data = array_diff($placeholders, [$name => $value]);

                // Translate placeholders value and place it in the main $placeholder array
                $placeholders[$name] = $this->translate(ltrim($value, '&'), $data);
            }
        }

        // We check for {{&...}} strings in the resulting message.
        // While the previous loop pre-translated placeholder value, this one
        // pre-translate the message string vars
        // We use some regex magic to detect them !
        $message = preg_replace_callback('/{{&(([^}]+[^a-z]))}}/', function ($matches) use ($placeholders) {
            return $this->translate($matches[1], $placeholders);
        }, $message);

        // Now it's time to replace the remaining placeholder. We use Twig do to this.
        // It's a bit slower, but allows to use the many Twig filters
        // See: http://twig.sensiolabs.org/doc/2.x/
        $template = $this->twig->createTemplate($message);
        $message = $template->render($placeholders);

        // Done !
        return $message;
    }

    /**
     * Determine which plural form we should use.
     * For some languages this is not as simple as for English.
     *
     * @param  int|float $number    The number we want to get the plural case for. Float numbers are floored.
     * @param  mixed     $forceRule False to use the plural rule of the language package
     *                              or an integer to force a certain plural rule
     * @return int       The plural-case we need to use for the number plural-rule combination
     */
    public function getPluralForm($number, $forceRule = false)
    {
        // Default to English rule (1) or the forced one
        $ruleNumber = ($forceRule !== false) ? $forceRule : (($this->has('@PLURAL_RULE')) ? $this->get('@PLURAL_RULE') : 1);

        // Get the rule class
        $class = "\UserFrosting\I18n\PluralRules\Rule$ruleNumber";
        if (!class_exists($class)) {
            throw new \OutOfRangeException("The rule number '$ruleNumber' must be between 0 and 16. ($class)");
        }

        return $class::getRule((int) $number);
    }
}
