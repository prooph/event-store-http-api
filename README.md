# Prooph EventStore HTTP API

[![Build Status](https://travis-ci.org/prooph/event-store-http-api.svg?branch=master)](https://travis-ci.org/prooph/event-store-http-api)
[![Coverage Status](https://coveralls.io/repos/github/prooph/event-store-http-api/badge.svg?branch=master)](https://coveralls.io/github/prooph/event-store-http-api?branch=master)
[![Gitter](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/prooph/improoph)

## Overview

Prooph Event Store is capable of persisting event messages that are organized in streams. `Prooph\EventStore\EventStore`
itself is a facade for different persistence adapters (see the list below) and adds event-driven hook points for `Prooph\EventStore\Plugin\Plugin`s
which make the Event Store highly customizable.

The HTTP API is a standalone software that exposes event streams via HTTP protocol.

## Installation

1)
    cp composer.json.dist composer.json
    cp config/pipeline.php.dist config/pipeline.php
    cp config/autoload/event_store.local.php.dist config/autoload/event_store.local.php

Make your adjustments.

2)

open your database console and create a database

3)

create your event streams table, see: https://github.com/prooph/pdo-event-store/tree/master/scripts

5)

php -S 0.0.0.0:8080 -t public/.

## Writing to streams

Open a text file "foo.txt" and put the following content:

    [
      {
        "uuid": "f9fea0b9-bbab-41ad-b3c1-56e09a1044a4",
        "created_at": "2016-11-12T14:35:41.702700",
        "message_name": "event-type",
        "payload": {
          "a": "2"
        },
        "metadata":{"_aggregate_version":1}
      },
      {
        "message_name":"foo",
        "payload":{"b" : "c"},
        "metadata":{"_aggregate_version":2}
      }
    ]

Then run this command:

    curl -i -d @foo.txt http://localhost:8080/streams/Prooph%5CModel%5CUser -H "Content-Type: application/vnd.eventstore.atom+json"

You should see:

    HTTP/1.1 201 Created

And on a second request due to duplicate _aggregate_version:

    HTTP/1.1 500 Cannot create or append to stream

## Reading from streams

    curl -i http://localhost:8080/streams/Prooph%5CModel%5CUser/1 -H "Accept: application/vnd.eventstore.atom+json"

    curl -i http://localhost:8080/streams/Prooph%5CModel%5CUser/11/forward/20 -H "Accept: application/vnd.eventstore.atom+json"

    curl -i http://localhost:8080/streams/Prooph%5CModel%5CUser/71/backward/42 -H "Accept: application/vnd.eventstore.atom+json"

    curl -i http://localhost:8080/streams/Prooph%5CModel%5CUser/head/backward/2 -H "Accept: application/vnd.eventstore.atom+json"

## Support

- Ask questions on [prooph-users](https://groups.google.com/forum/?hl=de#!forum/prooph) mailing list.
- File issues at [https://github.com/prooph/event-store-http-api/issues](https://github.com/prooph/event-store-http-api/issues).
- Say hello in the [prooph gitter](https://gitter.im/prooph/improoph) chat.

## Contribute

Please feel free to fork and extend existing or add new plugins and send a pull request with your changes!
To establish a consistent code quality, please provide unit tests for all your changes and may adapt the documentation.

## License

Released under the [New BSD License](LICENSE).
