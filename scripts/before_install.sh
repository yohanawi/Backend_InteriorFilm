#!/bin/bash
set -e

STAGING=/opt/bitnami/apache/staging

echo "Cleaning staging directory"
rm -rf $STAGING
mkdir -p $STAGING