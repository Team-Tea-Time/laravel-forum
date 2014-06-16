# Laravel forum package

[![Build Status](https://travis-ci.org/atrakeur/laravel-forum.svg?branch=master)](https://travis-ci.org/atrakeur/laravel-forum)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/atrakeur/laravel-forum/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/atrakeur/laravel-forum/?branch=master)

## Goals

This package aims to provide a good starting point implementing a forum inside a laravel application.
It focus on taking care of all the tedious and repetiting work of forum creation (categories, subcategories, topics, messages). Allowing you to spend more time on you website features and how the forum integrates with you application.
This package will provide multiple hooks such as specials events and custom closures to allow you to customise his behavior. Additionnaly, you'll be able to extends forum's core classes to implement you own methods directly inside the core.

## Installation

Not yet realeased

## Features

This package is currently in heavy work, so all of the following features arn't in place and may not work yet

## Additionnal planned features

 * Category nesting on 2 levels
 * Topic and messages inside categories
 * Easy user integration (through a php interface)
 * Easy user right integration (through a php interface)
 * Message posting (with hooks for formatting)
 * Light weight & blasing fast (designed with caching and high speed in mind)

## Events

This package provides various events as hooks to enable you to implement you own functionnality on top of forum's functionnality.
Here is a complete list of all events, as to when they are fired. When a parameter is given, you can use this parameter to change a forum's iternal object to fit your needs.

| Events               | Params        | Usage                            |
| -------------        |:-------------:| ---------------------------------------------:                     |
| forum.new.topic      | $topic        | Called before topic save. Can be used to modify topic contents     |
| forum.new.message    | $message      | Called before message save. Can be used to modify message contents |
| forum.saved.topic    | $topic        | Called after topic save. Can be used for logging purposes          |
| forum.saved.message  | $message      | Called after message save. Can be used for logging purposes        |