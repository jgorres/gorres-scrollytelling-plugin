#!/bin/bash
clear

# ==================================================
# Script name: build.sh
# Author: Jörn Gorres
# Date: 01.10.2026
# Description: Builds the WordPress Playground bundle of the Gorres Scrollytelling
#              online help: exports the content of the local help site
#              (content.json, uploads.zip), copies the current release ZIP of
#              the plugin next to blueprint.json and mirrors the bundle into
#              the local checkout of the public GitHub repository.
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
SITE_DIR="${1:-/var/www/local-sites/gorres-scrollytelling.local}"
GITHUB_DIR="${ZIP_DIR}/${SLUG}-help"

# Files of the bundle that stay out of the GitHub repository, and files of the
# repository that are not part of the bundle.
MIRROR_EXCLUDES=(--exclude=/.git --exclude=/README.md --exclude=/LICENSE --exclude=/build.sh --exclude=/export.php)

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

for tool in wp php python3 rsync git; do
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

# The GitHub checkout is optional, but when it exists it must be clean:
# the mirror would otherwise overwrite edits made there without a trace.
MIRROR=0
if git -C "${GITHUB_DIR}" rev-parse --is-inside-work-tree > /dev/null 2>&1; then
	MIRROR=1
	STATUS="$(git -C "${GITHUB_DIR}" status --porcelain)"
	if [[ -n "${STATUS}" ]]; then
		echo "Fehler: '${GITHUB_DIR}' hat nicht committete Änderungen:" >&2
		echo "${STATUS}" | sed 's/^/  /' >&2
		echo "Zuerst dort committen und pushen oder die Änderungen verwerfen." >&2
		exit 1
	fi
fi

echo "Hilfe-Site:    ${SITE_DIR}"
echo "Plugin-ZIP:    ${PLUGIN_ZIP}"
echo "GitHub-Ordner: ${GITHUB_DIR}"
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

# --------------------------------------------------
# 5. Mirror the bundle into the GitHub repository (commit and push by hand).
# --------------------------------------------------
if [[ "${MIRROR}" -eq 1 ]]; then
	rsync -rc --delete "${MIRROR_EXCLUDES[@]}" "${BUNDLE_DIR}/" "${GITHUB_DIR}/"
	echo "  ok  nach ${GITHUB_DIR} gespiegelt"
	STATUS="$(git -C "${GITHUB_DIR}" status --short)"
	if [[ -n "${STATUS}" ]]; then
		echo "      Dort warten Änderungen auf Commit und Push:"
		echo "${STATUS}" | sed 's/^/      /'
	else
		echo "      GitHub-Ordner war schon auf diesem Stand."
	fi
else
	echo "Hinweis: '${GITHUB_DIR}' ist kein Git-Repository, nichts gespiegelt."
fi

echo ""
echo "Bundle: ${BUNDLE_DIR}"
echo ""
echo "===================================================="
echo "   Fertig!"
echo "===================================================="
