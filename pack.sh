#!/usr/bin/env bash

zip -r cagent-$0.zip \
    _meta \
    htdocs \
    plib \
    sbin \
    var \
    CHANGES.md \
    DESCRIPTION.md \
    meta.xml \
    composer.json \
    composer.lock