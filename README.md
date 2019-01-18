# `bayes`: A Naive-Bayes classifier for PHP
[![Build Status](https://travis-ci.org/niiknow/bayes.svg?branch=master)](https://travis-ci.org/niiknow/bayes)

`bayes` takes a document (piece of text), and tells you what category that document belongs to.

This library was ported from a nodejs lib @ https://github.com/ttezel/bayes

* Proven and popular classifier in nodejs - https://www.npmjs.com/package/bayes
* We kept the json serialization signature so you can simply use the learned/trained json output from both PHP and nodejs library.

## What can I use this for?

You can use this for categorizing any text content into any arbitrary set of **categories**. For example:

- is an email **spam**, or **not spam** ?
- is a news article about **technology**, **politics**, or **sports** ?
- is a piece of text expressing **positive** emotions, or **negative** emotions?

## Installing

```
composer install niiknow/bayes
```

## Usage

```php
$classifier = new \Niiknow\Bayes();

// teach it positive phrases

$classifier->learn('amazing, awesome movie!! Yeah!! Oh boy.', 'positive');
$classifier->learn('Sweet, this is incredibly, amazing, perfect, great!!', 'positive');

// teach it a negative phrase

$classifier->learn('terrible, shitty thing. Damn. Sucks!!', 'negative');

// now ask it to categorize a document it has never seen before

$classifier->categorize('awesome, cool, amazing!! Yay.');
// => 'positive'

// serialize the classifier's state as a JSON string.
$stateJson = $classifier->toJson();

// load the classifier back from its JSON representation.
$classifier->fromJson($stateJson);

```

## API

### `$classifier = new \Niiknow\Bayes([options])`

Returns an instance of a Naive-Bayes Classifier.

Pass in an optional `options` object to configure the instance. If you specify a `tokenizer` of `iTokenizer` instance in `options`, it will be used as the instance's tokenizer.

### `$classifier->learn(text, category)`

Teach your classifier what `category` the `text` belongs to. The more you teach your classifier, the more reliable it becomes. It will use what it has learned to identify new documents that it hasn't seen before.

### `$classifier->categorize(text)`

Returns the `category` it thinks `text` belongs to. Its judgement is based on what you have taught it with **.learn()**.

### `$classifier->toJson()`

Returns the JSON representation of a classifier.

### `$classifier->fromJson(jsonStr)`

Returns a classifier instance from the JSON representation. Use this with the JSON representation obtained from `$classifier->toJson()`

## MIT

