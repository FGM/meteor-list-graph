# DOTify Meteor list --tree

## Purpose

Convert the output of the Meteor > 1.5.2 command to GraphViz DOT format.

## Usage

```bash
# In the Meteor application directory:
meteor list --tree > /tmp/app.txt

# In the dotify directory:
composer install
php dotify.php /tmp/app.txt | dot -Tsvg > deps.svg
```

## Running tests

In the dotify directory:
```
composer install
vendor/bin/phpunit src/tests
```
