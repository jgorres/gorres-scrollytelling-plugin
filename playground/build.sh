#!/bin/bash
clear

# ==================================================
# Script name: build.sh
# Author: Jörn Gorres
# Date: 01.10.2026
# Description: Builds the WordPress Playground bundle of the Gorres Scrollytelling
#              online help: exports the content of the local help site
#              (content.json, uploads.zip) and copies the current release
#              ZIP of the plugin next to blueprint.json.
# ==================================================

set -euo pipefail

echo "===================================================="
echo "   GORRES SCROLLYTELLING: PLAYGROUND-BUNDLE ERZEUGEN"
echo "===================================================="
echo ""

BUNDLE_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
REPO_DIR="$(dirname "${BUNDLE_DIR}")"
SLUG="gorres-scrollytelling"
ZIP_DIR="${HOME}/dev/jgorres-im-WP-Repository"
SITE_DIR="${1:-/var/www/local-sites/scrollstage.local}"

# --------------------------------------------------
# 1. Check the inputs.
# --------------------------------------------------
if [[ $# -gt 1 ]]; then
	echo "Aufruf: $0 [Pfad der lokalen Hilfe-Site]" >&2
	exit 2
fi

if [[ ! -f "${SITE_DIR}/wp-load.php" ]]; then
	echo "Fehler: Keine WordPress-Installation unter '${SITE_DIR}'." >&2
	exit 1
fi

for tool in wp php python3; do
	if ! command -v "${tool}" > /dev/null 2>&1; then
		echo "Fehler: '${tool}' fehlt." >&2
		exit 1
	fi
done

VERSION="$(sed -n 's/^ \* Version:[[:space:]]*\([0-9.]*\).*/\1/p' "${REPO_DIR}/plugin/${SLUG}.php")"

if [[ -z "${VERSION}" ]]; then
	echo "Fehler: Version im Plugin-Header nicht gefunden." >&2
	exit 1
fi

PLUGIN_ZIP="${ZIP_DIR}/${SLUG}-${VERSION}.zip"

if [[ ! -f "${PLUGIN_ZIP}" ]]; then
	echo "Fehler: '${PLUGIN_ZIP}' fehlt. Zuerst ./build.sh im Repo ausführen." >&2
	exit 1
fi

echo "Hilfe-Site:    ${SITE_DIR}"
echo "Plugin-ZIP:    ${PLUGIN_ZIP}"
echo ""

# --------------------------------------------------
# 2. Export the content of the help site.
# --------------------------------------------------
wp --path="${SITE_DIR}" eval-file "${BUNDLE_DIR}/export.php" "${BUNDLE_DIR}"

# --------------------------------------------------
# 3. Copy the plugin ZIP into the bundle.
# --------------------------------------------------
cp "${PLUGIN_ZIP}" "${BUNDLE_DIR}/${SLUG}.zip"
echo "Plugin ${VERSION} nach ${SLUG}.zip kopiert."

# --------------------------------------------------
# 4. Validate the JSON files.
# --------------------------------------------------
for json in blueprint.json content.json; do
	if ! python3 -m json.tool "${BUNDLE_DIR}/${json}" > /dev/null; then
		echo "Fehler: '${json}' ist kein gültiges JSON." >&2
		exit 1
	fi
	echo "  ok  ${json}"
done

echo ""
echo "Bundle: ${BUNDLE_DIR}"
echo ""
echo "===================================================="
echo "   Fertig!"
echo "===================================================="
