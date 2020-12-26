# Documentation

Push all keys to TranslationCaptain
Pull all keys and translation from TranslationCaptain
Generate language files in laravel php format from the pull of TranslationCaptain
Upload a screenshot of the usages of keys to TranslationCaptain

# Setup
in _translationcaptain.blade.php
you need to implement the getLanguage function to return the 2 letter language code of the current language of your view
this will be used to store teh language with the screenshot





# Adding a key
1) Add the key to your code, run 
Sync (is the same as push and then pull)
   
2) Add the key to any language file
Sync (is the same as push and then pull)

When a new key is being displayed (parsed by the translator), it is also logged in the queue for uploading on the next push

# Translations
All translations happen in TranslationCaptain and on the next pull the local translation files are overwritten with the newly pulled files

## Prinicples


## Changes
* [TODO ITEMS](todo.md)
* [CHANGELOG](../CHANGELOG.md)

