# Prooph EventStore HTTP API

[![Build Status](https://travis-ci.org/prooph/event-store-http-api.svg?branch=master)](https://travis-ci.org/prooph/event-store-http-api)
[![Coverage Status](https://coveralls.io/repos/prooph/event-store-http-api/badge.svg?branch=master&service=github)](https://coveralls.io/github/prooph/event-store-http-api?branch=master)
[![Gitter](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/prooph/improoph)

## Overview

Prooph Event Store is capable of persisting event messages that are organized in streams. `Prooph\EventStore\EventStore`
itself is a facade for different persistence adapters (see the list below) and adds event-driven hook points for `Prooph\EventStore\Plugin\Plugin`s
which make the Event Store highly customizable.

The HTTP API is a standalone software that exposes event streams via HTTP protocol.

## Installation

Todo!

## Support

- Ask questions on [prooph-users](https://groups.google.com/forum/?hl=de#!forum/prooph) mailing list.
- File issues at [https://github.com/prooph/event-store-http-api/issues](https://github.com/prooph/event-store-http-api/issues).
- Say hello in the [prooph gitter](https://gitter.im/prooph/improoph) chat.

## Contribute

Please feel free to fork and extend existing or add new plugins and send a pull request with your changes!
To establish a consistent code quality, please provide unit tests for all your changes and may adapt the documentation.

## License

Released under the [New BSD License](LICENSE).
