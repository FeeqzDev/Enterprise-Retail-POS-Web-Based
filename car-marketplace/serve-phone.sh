#!/usr/bin/env bash
# Serve the marketplace on your Wi-Fi so you can open it on your phone.
# Uses nip.io (free wildcard DNS): toyota.<your-ip>.nip.io resolves to <your-ip>,
# so broker subdomains work on a phone with no setup.
set -euo pipefail
cd "$(dirname "$0")"

PORT="${PORT:-8000}"
IP="${1:-}"
if [ -z "$IP" ]; then
  IP="$(ipconfig getifaddr en0 2>/dev/null || ipconfig getifaddr en1 2>/dev/null || hostname -I 2>/dev/null | awk '{print $1}' || true)"
fi
if [ -z "$IP" ]; then
  echo "Couldn't detect your Wi-Fi IP. Run: ./serve-phone.sh 192.168.x.x" >&2
  exit 1
fi

BASE="${IP}.nip.io"
echo
echo "Open these on your phone (same Wi-Fi as this computer):"
echo "  Main site  http://${BASE}:${PORT}"
for broker in toyota proton perodua honda; do
  printf "  %-10s http://%s.%s:%s\n" "$broker" "$broker" "$BASE" "$PORT"
done
echo
echo "Press Ctrl+C to stop."
BASE_DOMAIN="$BASE" php -S "0.0.0.0:${PORT}" -t public public/index.php
