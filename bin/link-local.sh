#!/bin/bash
# Symlinks this repo's theme and mu-plugin into the Local (by Flywheel) site,
# so edits here show up on http://opendesknetwork.test immediately.
set -euo pipefail
REPO="$(cd "$(dirname "$0")/.." && pwd)"
SITE="${LOCAL_SITE:-$HOME/Local Sites/opendesknetwork/app/public}"
WPC="$SITE/wp-content"

mkdir -p "$WPC/mu-plugins"
ln -sfn "$REPO/wp-content/themes/odn" "$WPC/themes/odn"
ln -sfn "$REPO/wp-content/mu-plugins/odn-network.php" "$WPC/mu-plugins/odn-network.php"
ln -sfn "$REPO/wp-content/mu-plugins/odn-network" "$WPC/mu-plugins/odn-network"
echo "Linked theme and mu-plugin into $WPC"
