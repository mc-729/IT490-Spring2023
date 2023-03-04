#!/usr/bin/env bash
flask db upgrade
flask --debug run --host=0.0.0.0