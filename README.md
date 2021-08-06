# Search Adds
This is a Drupal module developed to meet a specific client request.  Existing modules, like [Search API Spellcheck](https://www.drupal.org/project/search_api_spellcheck) were close but not right for the requested behavior.  This module allows you to set very specific trigger words and have custom text responses print to the search page when one of those triggers is present.

## Instalation
Install by normal means.  As this is not yet in the drupal community it cannot be fetched with composer; download the module to your webroot/modules/custom folder and then enable either with drush or in the site ui.

## Configuration
- The module creates the page '/admin/config/search/search-adds'
- Set your custom triggers and responses on that page
- Add the response region to the View holding your search results, typically in the Header space

![image](https://user-images.githubusercontent.com/5340576/128545001-999fcf54-0b98-4ecf-b2b8-e86d72cab2b3.png)


## Disclaimer
This module is still narrowly tested, with specific usage and acceptance criteria.  When multiple triggers are present in the search query, only the first match is returned.  This means the order of entries on the configuration page is significant.  Minimal fuzzy logic is included, and exact matches are given priority.
```php
    // All letters to lowercase and remove punctuation.
    $fuzzy_search = preg_replace('/[^a-z0-9]/', '', strtolower($query));

```
