#!/bin/bash

##################################################################
# Script de vérification : Interdiction de <script> dans les Twig
# Échoue si du JavaScript est détecté dans les fichiers .html.twig
##################################################################

set -e

TEMPLATES_DIR="templates"
EXIT_CODE=0

echo "🔍 Vérification : Pas de balise <script> dans les fichiers Twig..."

# Rechercher toutes les occurrences de <script dans les fichiers .html.twig
VIOLATIONS=$(grep -rnE '<script' "$TEMPLATES_DIR" --include="*.html.twig" || true)

if [ -n "$VIOLATIONS" ]; then
    echo ""
    echo "❌ ERREUR : Balise <script> détectée dans les fichiers Twig !"
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
    echo "$VIOLATIONS"
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
    echo ""
    echo "📋 RÈGLE : Séparation des préoccupations"
    echo "   Le JavaScript doit être dans des fichiers .js séparés,"
    echo "   et chargé via asset() ou importmap."
    echo ""
    EXIT_CODE=1
else
    echo "✅ Aucune violation détectée : Tous les templates sont conformes."
fi

exit $EXIT_CODE
